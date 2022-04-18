<?php

class Database{

    private static $instance;
    private $pdo;

    public static function getInstance(){
        if( !self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        $ini_array = parse_ini_file("config.ini");

        $DB_HOST = $ini_array['hostname'];
        $DB_NAME = $ini_array['database'];
        $DB_USER = $ini_array['username'];
        $DB_PASS = $ini_array['password'];
        $DB_TYPE = $ini_array['dbtype'];

        $this -> pdo = new PDO($DB_TYPE.":host=".$DB_HOST.";dbname=".$DB_NAME,
                            $DB_USER,
                            $DB_PASS);

        $this -> pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this -> pdo -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this -> pdo -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    }

    public function getConnection(){
        return $this -> pdo;
    }

    public function closeConnection(){
        $this -> pdo = null;
    }

}

?>