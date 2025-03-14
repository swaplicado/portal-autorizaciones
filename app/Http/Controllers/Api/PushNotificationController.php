<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Validar que titles y cuerpo esten presentes
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        // validar que en los ids de usuario o externos existan
        $request->validate([
            'user_ids' => 'required_without:external_ids',
            'external_ids' => 'required_without:user_ids',
        ]);

        $title = $request->title;
        $body = $request->body;
        $externalIds = $request->external_ids;
        $userIds = $request->user_ids;

        // arreglo de parámetros:
        $oData = $request->data;
        $sSound = $request->sound;
        $iBadge = $request->badge;

        return $this->sendPushNotification($title, $body, $externalIds, $userIds, $oData, $sSound, $iBadge);
    }

    public function sendPushNotification($title, $body, $externalIds, $userIds, $oData, $sSound, $iBadge) {
        // Validar que los parámetros tengan un valor válido:
        if (empty($title) || empty($body)) {
            return response()->json(['error' => 'Title and body are required.'], 400);
        }
        if (empty($externalIds) && empty($userIds)) {
            return response()->json(['error' => 'User IDs or external IDs are required.'], 400);
        }

        // Obtener los tokens de los usuarios
        $lUsers = [];
        if (!empty($userIds)) {
            $users = \App\Models\User::whereIn('id', $userIds)
                    ->whereNotNull('expo_token')
                    ->get();
            Log::info('sendPushNotification, users: ' . json_encode($users));
            $lUsers = array_merge($lUsers, $users->toArray());
        }
        if (!empty($externalIds)) {
            $externalUsers = \App\Models\User::whereIn('external_id_n', $externalIds)
                                ->whereNotNull('expo_token')
                                ->get();
            Log::info('sendPushNotification, externalUsers: ' . json_encode($externalUsers));
            $lUsers = array_merge($lUsers, $externalUsers->toArray());
        }

        // Eliminar usuarios repetidos en array:
        $lUsers = array_unique($lUsers, SORT_REGULAR);
        if (empty($lUsers)) {
            return response()->json(['error' => 'Usuarios no encontrados para la notificación.'], 404);
        }

        // obtener data
        $aData = [
            "title" => $title,
            "body" => $body,
            // transformar oData a objeto
            "data" => new \stdClass(),
            "sound" => $sSound
        ];

        if (! empty($iBadge)) {
            $aData['badge'] = $iBadge;
        }

        $url = 'https://exp.host/--/api/v2/push/send';
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        foreach ($lUsers as $key => $oUser) {
            // Realizar la solicitud a la API de Expo
            $ch = curl_init($url);
            $aData['to'] = $oUser['expo_token'];
            
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aData)); // Convierte array a JSON
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // No espera la respuesta
            curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Cierra rápido la conexión
            curl_setopt($ch, CURLOPT_HEADER, false);

            curl_exec($ch);
            curl_close($ch);

            // sleep de 1 segundo
            sleep(1);
        }

        return response()->json(['success' => 'Notificación enviada correctamente.'], 200);
    }
}
