<?php namespace App\Notifications;

use Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;
use App\Models\User;

class Core {

    /**
     * Enviar notificación a un usuario específico mediante array de ids
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public static function sendNotificationToUsers(array $toUsers, string $message, string $title = "")
    {
        // Verificar si el array de usuarios está vacío
        if (empty($toUsers)) {
            Log::error('No se especificaron usuarios a los que enviar la notificación.');

            return [
                'status' => 'error',
                'message' => 'No se especificaron usuarios a los que enviar la notificación.',
                'code' => 400
            ];
        }
        
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:edwin.carmona@swaplicado.com.mx',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        $subscriptions = PushSubscription::whereIn('user_id', $toUsers)
                                            ->orderBy('created_at', 'DESC')
                                            ->get();

        if ($subscriptions->isEmpty()) {
            Log::error('No se encontraron suscripciones para los usuarios especificados. ' . (implode(", ", $toUsers)));

            return [
                'status' => 'error',
                'message' => 'No se encontraron suscripciones para los usuarios especificados. ' . (implode(", ", $toUsers)),
                'code' => 404
            ];
        }

        try {
            $notificationTitle = $title;

            foreach ($subscriptions as $sub) {
                if (empty($notificationTitle))
                    $notificationTitle = "Notificación SIIE APP";

                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]);
                
                $payload = json_encode([
                    'title' => $notificationTitle,
                    'body' => $message,
                ]);
                
                Log::info('Enviando notificación a ' . $sub->user_id . ' ... ' . $notificationTitle);

                $webPush->sendOneNotification($subscription, $payload);
            }

            return ['status' => 'success'];
        }
        catch (\Throwable $th) {
            Log::error($th);

            return [
                'status' => 'error',
                'message' => 'Error al enviar notificación a los usuarios especificados. ' . $th->getMessage(),
                'code' => 500
            ];
        }
    }

    public static function notifyToExternalUsersAboutOC($aExternalUsers, $sFolio) {
        try {
            $message = "¡Hola! tienes una nueva orden de compra por autorizar. Folio: " . $sFolio;
            $aUsers = User::whereIn('external_id_n', $aExternalUsers)->pluck('id')->toArray();
            $title = "Nueva orden de compra por autorizar [" . $sFolio . "]";

            return self::sendNotificationToUsers($aUsers, $message, $title);

        } catch (\Throwable $th) {
            Log::error($th);

            return [
                'status' => 'error',
                'message' => 'Error al enviar notificación a los usuarios externos. ' . $th->getMessage(),
                'code' => 500
            ];
        }
    }

}