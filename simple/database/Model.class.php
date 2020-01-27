<?php

class Mysql
{
    protected $conn;
    protected $sql;

    /**
     * Constructor, to connect to database, select database and set charset
     * @param $config array configuration
     */
    public function __construct($config = array())
    {
        $host = isset($config['host'])? $config['host'] : 'localhost';

        $user = isset($config['user'])? $config['user'] : 'root';

        $password = isset($config['password'])? $config['password'] : '';

        $dbname = isset($config['dbname'])? $config['dbname'] : '';

        $port = isset($config['port'])? $config['port'] : '3306';

        $charset = isset($config['charset'])? $config['charset'] : '3306';

        $this->conn = mysqli_connect("$host:$port",$user,$password) or die('Database connection error');

        mysqli_select_db($dbname) or die('Database selection error');

        $this->setChar($charset);
    }

    /**
     * Set charset
     * @access private
     * @param $charset string charset
     */
    private function setChar($charset)
    {
        $sql = 'set names ' . $charset;
        $this->query($sql);
    }

    /**
     * Execute SQL statement
     * @access public
     * @param $sql string SQL query statement
     * @return bool|mysqli_result $result，if succeed, return resrouces; if fail return error message and exit
     */
    public function query($sql)
    {
        $this->sql = $sql;

        $str = $sql . " [" . date('"Y-m-d H:i:s"') . "] " . PHP_EOL;

        file_put_contents('log.txt', $str, FILE_APPEND);

        $result = mysqli_query($this->sql, $this->conn);

        if (!$result) {
            die($this->errno().':'.$this->error().'<br />Error SQL statement is '.$this->sql.'<br />');
        }
        return $result;
    }

    /**
     * Get the first column of the first record
     * @access public
     * @param $sql string SQL query statement
     * @return array|bool|null the value of this column
     */
    public function getOne($sql)
    {
        $result = $this->query($sql);

        $row = mysqli_fetch_row($result);

        return $row ? $row[0] : false;
    }

    /**
     * Get one record
     * @access public
     * @param $sql string SQL query statement
     * @return array|bool associative array
     */
    public function getRow($sql)
    {
        if ($result = $this->query($sql)) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }

    /**
     * Get all records
     * @access public
     * @param $sql string SQL query statement
     * @return array $list an 2D array containing all result records
     */
    public function getAll($sql)
    {
        $result = $this->query($sql);
        $list = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        return $list;
    }

    /**
     * Get the value of a column
     * @access public
     * @param $sql string SQL query statement
     * @return array $list array an array of the value of this column
     */
    public function getCol($sql)
    {
        $result = $this->query($sql);

        $list = array();

        while($row = mysqli_fetch_row($result)) {
            $list[] = $row[0];
        }
        return $list;
    }
}