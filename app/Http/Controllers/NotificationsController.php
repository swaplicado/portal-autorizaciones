<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

class NotificationsController extends Controller
{
    public function enviarNotificacion(Request $request)
    {
        $userId = 76;
        $title = "Notificación de prueba";
        $message = "Esta es una notificación de prueba";

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:edwin.carmona@swaplicado.com.mx',
                'publicKey' => file_get_contents(storage_path(env('VAPID_PUBLIC_KEY'))),
                'privateKey' => file_get_contents(storage_path(env('VAPID_PRIVATE_KEY'))),
            ],
        ];

        $webPush = new WebPush($auth);

        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $message,
            ]);

            $webPush->sendOneNotification($subscription, $payload);
        }

        return "Notificación enviada!";
    }
}
