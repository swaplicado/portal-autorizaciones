<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class PushNotificationController extends Controller
{

    public function sendGenericNotification(Request $request)
    {
        // Validar que idUser y folio estén presentes
        $request->validate([
            'idUser' => 'required',
            'folio' => 'required',
        ]);

        $idUser = $request->idUser;
        $folio = $request->folio;

        // Crear title y body con mensaje genérico
        $title = "Notificación de sistema";
        $body = "Estimado usuario, su folio " . $folio . " ha sido procesado.";

        // Inicializar otros parámetros
        $oData = new \stdClass();
        $sSound = false;
        $iBadge = 0;

        // Llamar a la función sendPushNotification
        return $this->sendPushNotification($title, $body, null, [$idUser], $oData, $sSound, $iBadge);
    }

    public function sendExternalGenericNotification(Request $request)
    {
        // Validar que idUser y folio estén presentes
        $request->validate([
            'idExternalUser' => 'required'
        ]);

        $idUser = $request->idExternalUser;
        $folio = $request->folio;
        $porpouse = $request->porpouse;

        // Crear title y body con mensaje genérico
        $title = "Notificación de sistema";
        $body = "Estimado usuario, su folio " . $folio . " ha sido procesado.";
        $sSound = "default";

        switch ($porpouse) {
            // Nueva OC por autorizar
            case '1':
                $title = "[OC ".$folio."] Nueva OC por autorizar";
                $body = "¡Hola! Tienes una nueva OC por autorizar.";
                $iBadge = 1;
                break;
            // OC autorizada
            case '2':
                $title = "[OC ".$folio."] OC autorizada";
                $body = "Hola, la OC " . $folio . " ha sido autorizada.";
                $iBadge = 1;
                break;
            // OC rechazada
            case '3':
                $title = "[OC ".$folio."] OC rechazada";
                $body = "Hola, la OC " . $folio . " ha sido rechazada.";
                $iBadge = 1;
                break;
            case '4':
                $counter = $request->counter;
                $title = "Tienes " . $counter . " OCs por autorizar";
                $body = "¡Hola! Tienes " . $counter . " OCs por autorizar.";
                $iBadge = $counter;
                break;
            case '5':
                $counter = $request->counter;
                $title = "";
                $body = "";
                $iBadge = $counter;
                break;
            default:
                $body = "Estimado usuario, su folio " . $folio . " ha sido procesado.";
                break;
        }

        // Inicializar otros parámetros
        $oData = new \stdClass();

        // Llamar a la función sendPushNotification
        return $this->sendPushNotification($title, $body,  [$idUser], null, $oData, $sSound, $iBadge);
    }
    
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
        // if (empty($title) || empty($body)) {
        //     return response()->json(['error' => 'Title and body are required.'], 400);
        // }
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

        $multiCurl = [];
        $mh = curl_multi_init();

        foreach ($lUsers as $key => $oUser) {
            $ch = curl_init($url);
            $aData['to'] = [$oUser['expo_token']];
            Log::info('sendPushNotification, data: ' . json_encode($aData));

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Espera respuesta
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Aumentar timeout
            curl_setopt($ch, CURLOPT_HEADER, false);

            curl_multi_add_handle($mh, $ch);
            $multiCurl[] = $ch;
        }

        // Ejecutar múltiples solicitudes en paralelo
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        // Obtener respuestas y cerrar conexiones
        foreach ($multiCurl as $ch) {
            $response = curl_multi_getcontent($ch);
            if (curl_errno($ch)) {
                Log::error('Curl error: ' . curl_error($ch));
            } else {
                Log::info('sendPushNotification, response: ' . $response);
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);

        return response()->json(['success' => 'Notificación enviada correctamente.'], 200);
    }
}
