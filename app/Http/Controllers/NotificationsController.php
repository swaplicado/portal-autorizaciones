<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\Core;
use Log;

class NotificationsController extends Controller
{
    /**
     * Enviar notificación a un usuario específico mediante http request
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function enviarNotificacion(Request $request)
    {
        // Obtener el parámetro idUser del request
        $idUser = $request->get('idUser');

        // Verificar si el idUser está presente
        if (! $idUser) {
            Log::error('No se especificaron usuarios a los cuales enviar la notificación.');
            return response()->json([
                'message' => 'No se especificaron usuarios a los cuales enviar la notificación.'
            ], 400);
        }

        // Convertir idUser en un array si no lo es
        $toUsers = is_array($idUser) ? $idUser : [$idUser];

        // Recibir parámetros title y message con valores por defecto
        $message = $request->get('message', 'Notificación de SIIE APP');
        $title = $request->get('title', 'Notificación');

        // Enviar la notificación
        $result = Core::sendNotificationToUsers($toUsers, $message, $title);

        // Verificar el resultado y responder en consecuencia
        if ($result['status'] === 'error') {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json(['message' => 'Notificación enviada!']);
    }

    public function notificationByUser(Request $request) {
        $idUser = $request->get('idUser');

        if (! $idUser) {
            Log::error('No se especificaron usuarios a los cuales enviar la notificación.');
            return response()->json([
                'message' => 'No se especificaron usuarios a los cuales enviar la notificación.'
            ], 400);
        }

        $toUsers = is_array($idUser) ? $idUser : [$idUser];
        $sFolio = $request->get('folio', 'NA');

        $result = Core::notifyToExternalUsersAboutOC($toUsers, $sFolio);

        if ($result['status'] === 'error') {
            return response()->json(['message' => $result['message']], $result['code']);
        }

        return response()->json(['message' => 'Notificación enviada!']);
    }

    private function convertPublicKeyToBase64Url($filePath, $varEnv) {
        $keyContent = file_get_contents($filePath);

        // 1. Eliminar encabezado y pie
        $keyContent = preg_replace("/-----.*?-----/", "", $keyContent);

        // 2. Eliminar saltos de línea y espacios
        $keyContent = str_replace(["\n", "\r", " "], "", $keyContent);

        // 3. Convertir Base64 a formato URL Safe
        $keyContent = str_replace(['+', '/', '='], ['-', '_', ''], $keyContent);
    
        // Guardar la clave en el archivo .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
    
        if (strpos($envContent, $varEnv . '=') !== false) {
            // Reemplazar la línea existente
            $envContent = preg_replace(
                "/^" . preg_quote($varEnv, '/') . "=.*/m",
                $varEnv . '=' . $keyContent,
                $envContent
            );
        } else {
            // Agregar la nueva variable al final del archivo
            $envContent .= PHP_EOL . $varEnv . '=' . $keyContent;
        }
    
        file_put_contents($envPath, $envContent);
    
        return $keyContent;
    }
    
    public function setVapidKeys() {
        $publicKey = $this->convertPublicKeyToBase64Url(storage_path(env('VAPID_PUBLIC_KEY')), 'VAPID_PUBLIC_KEY_64');
        $privateKey = $this->convertPublicKeyToBase64Url(storage_path(env('VAPID_PRIVATE_KEY')), 'VAPID_PRIVATE_KEY_64');
        
        return response()->json([
                'message' => 'Claves VAPID actualizadas correctamente.',
                'public_key' => $publicKey,
                'private_key' => $privateKey
            ]);
    }
}
