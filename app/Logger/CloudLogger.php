<?php

namespace App\Logger;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Log;

class CloudLogger
{
    public static function log(string $severity, string $message): void
    {
        try {
            // Realizar petición https a cloud function de google:
            $timestamp = date('Y-m-d H:i:s');
            // minúsculas:
            $sSeverity = strtolower($severity);
            $logData = [
                'message' => $message,
                'severity' => $sSeverity,
                'timestamp' => $timestamp,
                'platform' => 'PHP'
            ];
            $loggerRoute = env('CLOUD_LOGGER_URL', '');
            if (empty($loggerRoute)) {
                switch ($sSeverity) {
                    case 'error':
                        Log::error($message);
                        break;
                    case 'warning':
                        Log::warning($message);
                        break;
                    default:
                        Log::info($message);
                        break;
                }

                throw new \Exception('CLOUD_LOGGER_URL not found in .env file');
            }
    
            $ch = curl_init($loggerRoute);
    
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($logData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // No espera la respuesta
            curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Cierra la conexión rápido
            curl_setopt($ch, CURLOPT_HEADER, false);
    
            curl_exec($ch);
            curl_close($ch);
        }
        catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
