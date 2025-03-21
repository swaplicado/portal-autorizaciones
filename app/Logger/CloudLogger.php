<?php

namespace App\Logger;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Log;

class CloudLogger
{

    /**
     * Crea el log de un mensaje de manera local y en la nube a través de google logging.
     *
     * @param string $severity
     * @param string $message
     * @return void
     */
    public static function log(string $severity, string $message): void
    {
        try {
            $environment = env('APP_ENV', 'production');
            if ($environment !== 'production') {
                return;
            }

            // Realizar petición https a cloud function de google:
            $timestamp = date('Y-m-d H:i:s');
            // minúsculas:
            $sSeverity = strtolower($severity);
            $logData = [
                'severity' => $sSeverity,
                'timestamp' => $timestamp,
                'platform' => 'PHP'
            ];
            
            // validar si hay una sesión activa para obtener el usuario, si no continuar
            if (\Auth::check()) {
                $logData['username'] = \Auth::user()->username;
                $logData['external_id'] = \Auth::user()->external_id_n;
            }

            switch ($sSeverity) {
                case 'error':
                    Log::error($message);
                    Log::channel('cloud')->error($message, $logData);
                    break;
                case 'warning':
                    Log::warning($message);
                    Log::channel('cloud')->warning($message, $logData);
                    break;
                default:
                    Log::info($message);
                    Log::channel('cloud')->info($message, $logData);
                    break;
            }
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
