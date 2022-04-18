<?php

class TestController{

    public $controller;
    private $repository;
    private static $_instance;

    public static function getInstance(){
        if( !self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct(){
        $this->repository = new TestRepository();
        $this->controller = array(
            "POST.user"     => "insertar",
            "GET.user"      => "obtener",
            "GET.login"     => "login",
            "PUT.user"      => "actualizar",
            "DELETE.user"   => "eliminar"
        );
    }

    function login(){
        $userTest = new AuthResponse();
        $userTest -> username = "Admin";
        $userTest -> id = 1;
        $userTest -> rol = 1;
        $token = TokenTools::getInstance() -> createToken($userTest);
        $userTest -> accesToken = $token;
        $userTest -> tokenType = 'Bearer';
        return $userTest;
    }

    function obtener(){
        $rows = $this -> repository -> getAllUsers();
        return $rows;
    }

    function insertar(){
        $request = json_decode(file_get_contents('php://input'), TRUE);
        $nombre     = $request["nombre"];
        $apellido   = $request["apellido"];
        return "insertar :: ".$nombre.' '.$apellido;
    }

    function actualizar(){
        $request = json_decode(file_get_contents('php://input'), TRUE);
        return "Actualizar :: ".$request["nombre"];
    }

    function eliminar(){
        $id = $_GET["id"];
        return "eliminar :: ".$id;
    }

}

?>