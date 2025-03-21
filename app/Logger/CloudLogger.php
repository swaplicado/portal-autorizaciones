<?php

namespace App\Logger;

use Log;

class CloudLogger
{

    /**
     * Crea un log de manera local y otro en la nube a través de google logging.
     *
     * @param string $severity
     * @param string|object|array $oMessage
     * @return void
     */
    public static function log(string $severity, string $oMessage): void
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

            $sMessage = '';
            if (is_string($oMessage)) {
                $sMessage = $oMessage;
            } else {
                $sMessage = json_encode($oMessage);
            }

            switch ($sSeverity) {
                case 'error':
                    Log::error($oMessage);
                    Log::channel('cloud')->error($sMessage, $logData);
                    break;
                case 'warning':
                    Log::warning($oMessage);
                    Log::channel('cloud')->warning($sMessage, $logData);
                    break;
                default:
                    Log::info($oMessage);
                    Log::channel('cloud')->info($sMessage, $logData);
                    break;
            }
        }
        catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
