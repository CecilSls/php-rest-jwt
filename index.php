<?php
    header('Content-Type: text/html; charset=UTF-8');
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    //* Importaciones de PHP-JWT
    require_once("libs/jwt/src/BeforeValidException.php");
    require_once("libs/jwt/src/ExpiredException.php");
    require_once("libs/jwt/src/SignatureInvalidException.php");
    require_once("libs/jwt/src/JWT.php");
    require_once("libs/jwt/src/Key.php");

    //* Herramientas de Base de Datos, Generacion de Tokens, etc...
    require_once("inc/Database.php");
    require_once("inc/RequestAdm.php");
    require_once("inc/TokenTools.php");

    //* Cargar elementos
    $_folders = ['controllers', 'exceptions', 'models', 'repository']; //* Nombre de las carpetas a revisar
    foreach ($_folders as $folder) {
        foreach (scandir(dirname(__FILE__).'/'.$folder) as $filename) {
            $path = (dirname(__FILE__).'/'.$folder) . '/' . $filename;
            if (is_file($path)) {
                require_once $path;
            }
        }
    }

    $requestMethod = $_SERVER["REQUEST_METHOD"]; //* Obtencion del metodo request
    $uri = $_SERVER["REQUEST_URI"]; //* Obtencion del URI
    $uri = explode("/", $uri); //* Separación de elementos en base al caracter '/'
    //* Limpieza en metodos GET --
    if( isset($uri[2] ) && trim($uri[2]) != ""){
        $controller = $uri[2];

        if( $requestMethod === 'GET' || $requestMethod === 'DELETE' ){
            $controllerVars = explode("?", $controller);
            if( isset($controllerVars[1]) ){
                $controller = $controllerVars[0];
            }
        }
    }

    //* En caso de que no exista nada despues de la limpieza anterior, la petición no existe
    if( !isset($controller) ){
        header("HTTP/1.1 400 Bad Request");
        imprimeError("40x", "No se encontro la petición");
    }else{
        //* En caso contrario continua a la siguiente parte, procesar la petición.
        $mainController = new RequestAdm($requestMethod, $controller);
        $mainController -> prossesRequest();
    }

    //* FUNCIONES GENERALES para la respuesta de la API
    function imprimeJson($message) {
        $response = array(
            "ok" => true,
            "message" => $message, 
        );
        echo json_encode($response);
    }

    function imprimeError($errorCode, $mensaje) {
        $responseError = array(
            "ok" => false,
            "error" => $errorCode,
            "message" => $mensaje, 
        );
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode($responseError);
        throw new Exception();
    }

?>