<?php
class DB {
    private $host='localhost';
    private $user='root';
    private $pass='';
    private $dbname='website';
    //viết phương thức kết nối csdl
    protected $db;
    public function __construct(){
        $this->db=$this->Connect();
    }   
    public function Connect(){
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $this->user, $this->pass, $options);
        return $pdo;
    }
}