<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

define('SERVER', $_SERVER['HTTP_HOST'] );
define('SECRET_KEY', 'AGREGAR_KEY_AQUI');
define('ALGORITHM', 'HS256');
define('TIME_EXP', (60*10) ); // 10 minutos de exp

class TokenTools{

    private static $instance;

    public static function getInstance(){
        if( !self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getToken($token){
        try{
            if($token == null){ throw new AuthException();}
            $secretKey = base64_decode(SECRET_KEY);
            $decodeData = JWT::decode($token, new Key( $secretKey, ALGORITHM));
            $token = json_decode( json_encode($decodeData) );
            return $token;
        }catch(Exception $e){
            header("HTTP/1.1 401 Unauthorized");
            imprimeError("tknx004", $e->getMessage());
            throw new Exception();
        }
    }

    public function createToken(AuthResponse $response){
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;

        $expire = $notBefore + TIME_EXP; // más 1 hora
        $serverName = SERVER;

        $datos = [
            'id' => $response -> id,
            'name' => $response -> username,
            'tipo' => $response -> rol
        ];

        $data = [
            'iat' => $issuedAt, // cuando se genero el token
            'jti' => $tokenId, // identificador del token
            'iss' => $serverName, // servidor
            'exp' => $expire, // cuando expira
            'data' => $datos
        ];

        $secretKey = base64_decode(SECRET_KEY);

        $jwt = JWT::encode(
                        $data, //Data to be encoded in the JWT
                        $secretKey, // The signing key
                        ALGORITHM
        );

        $unencodedArray = $jwt;
        return $unencodedArray;
    }

    /* Funcion toma los datos del token dado para asÃ­ generar otro,
            de tal manera que no se tenga que volver a consultar en la db */

    function refreshToken($token_devuelto){
        $token = self::getToken($token_devuelto);
        $response = new AuthResponse();
        if (true || $token != null) {
            $response -> id = $token -> data -> id;
            $response -> username = $token -> data -> name;
            $response -> rol = $token-> data -> tipo;
            
            $new_token = self::createToken($response);
            $response -> tokenType = 'Bearer';
            $response -> accesToken = $new_token;
            
            return $response;
        }

        return null;
    }

    // Obtencion del Token desde el head de la peticion
    public function getBearerToken() {
        $headers = $this -> getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}

?>