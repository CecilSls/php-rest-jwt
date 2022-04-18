<?php

class RequestAdm{

    private $request;
    private $controllers;
    private $noAuth;

    public function __construct($method, $uri){
        $this -> request = $method.'.'.$uri;
        //* Array que contiene las peticiones que no deben llevar un token en el head
        $this -> noAuth = ["GET.login"];
        //* Obtención de todos los controladores a evaluarse
        $this -> controllers = array(
            "TestController"        => TestController::getInstance() -> controller,
        );
    }
    
    public function prossesRequest(){
        try{
            $request = null;
            $controller = "";

            //* Busqueda por medio de la cadena formada en el contructor, para averiguar si la petición existe
            foreach($this -> controllers AS $k => $v){
                $request = $v[$this -> request];
                if($request !== null){
                    $no_auth = in_array( $this -> request, $this -> noAuth );
                    if( !$no_auth ){
                        $tookenTool = TokenTools::getInstance();
                        $token = $tookenTool -> getBearerToken();
                        $ver_token = $tookenTool -> getToken($token);
                    }
                    $controller = $k;
                    break;
                }
            }

            if( (isset($request) && $request !== null) &&
                    isset($controller) && !empty($controller)){
                $response = $controller::getInstance() -> $request();
                if($response !== null){
                    imprimeJson($response);
                }else{
                    imprimeError("resNullx00", "Error al procesar la respuesta");
                }
            }else{
                header("HTTP/1.1 404 Not Found");
                imprimeError("req404x00", "Peticion no encontrada");
            }
        }catch(Error $error){
            imprimeError("40x", $error -> getMessage());
        }catch(AuthException $e){
            header("HTTP/1.1 401 Unauthorized");
            imprimeError("401x", $e->getMessage());
        }catch(PDOException $e){
            imprimeError("dbx200", $e->getMessage());
        }
    }
}

?>