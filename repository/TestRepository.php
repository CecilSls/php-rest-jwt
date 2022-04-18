<?php

class TestRepository{

    private static $_instance;
    private $pdo;

    public static function getInstance(){
        if( !self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct(){
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    public function getAllUsers(){
        $sql = "SELECT * FROM users";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt -> fetchAll();
        
        return $result;
    }
}

?>