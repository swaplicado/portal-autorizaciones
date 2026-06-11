<?php namespace App\Utils;

use GuzzleHttp\Client;
use App\Logger\CloudLogger;

class AppLinkUtils {
    public static function AppLinkLogin($oUser){
        $config = \App\Utils\Configuration::getConfigurations();

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Proxy-Token' => '1234$%&&'
        ];

        $client = new Client([
            'base_uri' => $config->AppLinkRoute,
            'timeout' => 10.0,
            'connect_timeout' => 5.0,
            'headers' => $headers,
            'verify' => false
        ]);

        $body = '{
                    "usr": "'.env('userAppLink', 'swapst').'",
                    "usr_pswd": "'.env('userAppLinkPass', '5w4p!*').'",
                    "reqUser": "'.$oUser->username.'"
                }';

        try {
            $response = $client->request('POST', $config->AppLinkRouteLogin , [
                'body' => $body
            ]);
        } catch (\Throwable $th) {
            CloudLogger::log('error', 'Error al intentar iniciar sesión en AppLink: ' . $th->getMessage());
            return null;
        }

        $jsonString = $response->getBody()->getContents();

        $data = json_decode($jsonString);

        return $data;
    }

    public static function getResources($oUser){
        $config = \App\Utils\Configuration::getConfigurations();
        $data = AppLinkUtils::AppLinkLogin($oUser);
        if(!is_null($data)){
            if($data->code != 200){
                return $data;
            }
        }else{
            return null;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $data->token
        ];
        
        $client = new Client([
            'base_uri' => $config->AppLinkRoute,
            'timeout' => 30.0,
            'headers' => $headers
        ]);

        $request = new \GuzzleHttp\Psr7\Request('GET', $config->AppLinkRouteGetResources, $headers);
        $response = $client->send($request);
        $jsonString = $response->getBody()->getContents();

        $data = json_decode($jsonString);
        return $data;
    }

    public static function requestAppLink($route, $method, $oUser, $body = null, $requireAuth = true, $parameters = null)
    {
        $config = \App\Utils\Configuration::getConfigurations();

        if ($requireAuth) {
            $data = AppLinkUtils::AppLinkLogin($oUser);
            if (!is_null($data)) {
                if ($data->code != 200) {
                    return $data;
                }
            } else {
                return null;
            }
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $data->token,
                'X-Proxy-Token' => '1234$%&&'
            ];
        } else {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Proxy-Token' => '1234$%&&'
            ];
        }


        $client = new Client([
            'base_uri' => $config->AppLinkRoute,
            'timeout' => 10.0,
            'connect_timeout' => 5.0,
            'headers' => $headers
        ]);

        $options = [];

        // Agregar parámetros de consulta para solicitudes GET
        if ($method === 'GET' && $parameters) {
            $options['query'] = $parameters;
        }

        // Agregar cuerpo para solicitudes POST, PUT, etc.
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && isset($body)) {
            $options['json'] = $body; // Asume que `$body` es un arreglo para JSON
        }

        // Enviar la solicitud
        try {
            $response = $client->request($method, $route, $options);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            CloudLogger::log('error', 'Error de conexión en requestAppLink [' . $method . ' ' . $route . ']: ' . $e->getMessage());
            return null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            CloudLogger::log('error', 'Error HTTP ' . $statusCode . ' en requestAppLink [' . $method . ' ' . $route . ']: ' . $e->getMessage());
            return null;
        } catch (\Throwable $th) {
            CloudLogger::log('error', 'Error inesperado en requestAppLink [' . $method . ' ' . $route . ']: ' . $th->getMessage());
            return null;
        }

        $jsonString = $response->getBody()->getContents();
        $data = json_decode($jsonString);
        return $data;
    }
}