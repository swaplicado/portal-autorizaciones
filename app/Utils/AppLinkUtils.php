<?php namespace App\Utils;

use GuzzleHttp\Client;
use App\Logger\CloudLogger;
use Log;

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
            'Authorization' => $data->token,
            'X-Proxy-Token' => '1234$%&&'
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
        
        // ⚠️ FORZAR Cloud Function URL (ignorar $config->AppLinkRoute)
        $cloudFunctionUrl = 'https://proxy-applink-954473475135.us-central1.run.app';
        
        // Construir headers base (siempre incluir X-Proxy-Token)
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Proxy-Token' => '1234$%&&'  // Siempre presente
        ];

        if ($requireAuth) {
            $data = AppLinkUtils::AppLinkLogin($oUser);
            if (!is_null($data)) {
                if ($data->code != 200) {
                    return $data;
                }
            } else {
                return null;
            }
            // Agregar Authorization
            $headers['Authorization'] = $data->token;
        }

        Log::info("Solicitud a AppLink [{$method} {$route}]: ", [
            'headers' => $headers,
            'body' => $body,
            'parameters' => $parameters
        ]);
        // ⚠️ IMPORTANTE: Pasar headers en la configuración Y en cada request
        $client = new Client([
            'base_uri' => $cloudFunctionUrl,  // 👈 Usar Cloud Function
            'timeout' => 15.0,                // Aumentar timeout
            'connect_timeout' => 10.0,
            'verify' => false,
            'http_errors' => false,           // Para manejar errores manualmente
        ]);

        $options = [
            'headers' => $headers  // 👈 Headers en cada request
        ];

        // Agregar parámetros de consulta para solicitudes GET
        if ($method === 'GET' && $parameters) {
            $options['query'] = $parameters;
        }

        // Agregar cuerpo para solicitudes POST, PUT, etc.
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && isset($body)) {
            $options['json'] = $body;
        }

        // Enviar la solicitud
        try {
            Log::info("Enviando solicitud a AppLink: {$method} {$route}");
            $response = $client->request($method, $route, $options);
            Log::info("Estado HTTP recibido: " . $response->getStatusCode());
            $jsonString = $response->getBody()->getContents();
            $data = json_decode($jsonString);
            
            // Si recibe 401, el token expiró - forzar nuevo login
            if ($response->getStatusCode() == 401 && $requireAuth) {
                // Limpiar token cacheado si estás usando caché
                $data = AppLinkUtils::AppLinkLogin($oUser);
                if ($data && $data->code == 200) {
                    // Reintentar con nuevo token
                    $options['headers']['Authorization'] = $data->token;
                    $response = $client->request($method, $route, $options);
                    $jsonString = $response->getBody()->getContents();
                    $data = json_decode($jsonString);
                }
            }
            Log::info("Respuesta del servidor externo (AppLink) para [{$method} {$route}]: " . $jsonString);
            return $data;
            
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("Error de conexión en requestAppLink [{$method} {$route}]: " . $e->getMessage());
            CloudLogger::log('error', "Error de conexión en requestAppLink [{$method} {$route}]: " . $e->getMessage());
            return null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("Error HTTP en requestAppLink [{$method} {$route}]: " . $e->getMessage());
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'Sin respuesta';
            CloudLogger::log('error', "Error HTTP {$statusCode} en requestAppLink [{$method} {$route}]: {$e->getMessage()} | Body: {$responseBody}");
            return null;
        } catch (\Throwable $th) {
            Log::error("Error inesperado en requestAppLink [{$method} {$route}]: " . $th->getMessage());
            CloudLogger::log('error', "Error inesperado en requestAppLink [{$method} {$route}]: " . $th->getMessage());
            return null;
        }
    }
}