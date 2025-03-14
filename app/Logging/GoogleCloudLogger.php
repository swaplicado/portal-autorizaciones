<?php
namespace App\Logging;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Logger;
use Google\Cloud\Logging\Handler\GoogleCloudLoggingHandler;
use Monolog\Handler\PsrHandler;

class GoogleCloudLogger
{
    public function __invoke(array $config)
    {
        // Inicializa Google Cloud Logging
        $logging = new LoggingClient([
            'keyFilePath' => env('GOOGLE_APPLICATION_CREDENTIALS')  // Usa las credenciales de servicio
        ]);

        // Asigna el logger de Google Cloud a Monolog
        $psrLogger = $logging->psrLogger('siie-portal-logger');
        
        return new Logger('cloud', [new PsrHandler($psrLogger)]);
    }
}
