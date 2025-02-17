<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

class NotificationsController extends Controller
{
    public function enviarNotificacion(Request $request)
    {
        // obtener arreglo de enteros del request llamado "toUsers"
        $toUsers = [];
        // recibir parámetro id_user
        if ($request->has('id_user')) {
            $toUsers[] = $request->id_user;
        }
        else {
            return response()->json([
                'message' => 'No se especificaron usuarios a los que enviar la notificación.'
            ], 400);
        }

        Log::info($toUsers);

        $message = "Esta es una notificación de prueba";

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:edwin.carmona@swaplicado.com.mx',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        $subscriptions = PushSubscription::whereIn('user_id', $toUsers)->get();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                // concatenar array toUsers: 
                'message' => 'No se encontraron suscripciones para los usuarios especificados. ' . (implode(", ", $toUsers))
            ], 404);
        }

        Log::info('Enviando notificación a: ' . $toUsers . ' con message: ' . $message);

        $title = "";
        foreach ($subscriptions as $sub) {
            $title = "Notificación de prueba usuario: ".$sub->user_id." ". date('Y-m-d H:i:s');
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $message,
            ]);

            Log::info('Enviando notificación a: ' . $sub->user_id . ' con title: ' . $title);

            $webPush->sendOneNotification($subscription, $payload);
        }

        return "Notificación enviada!";
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
