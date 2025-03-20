<?php

use App\Http\Controllers\Api\PushNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RequisitionsController;
use App\Http\Controllers\Api\DPSApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    // Test:
    Route::get('test', [RequisitionsController::class, 'test']);
    Route::get('dps-test', [DPSApiController::class, 'test']);

    /**
     * Rutas de DPS
     */
    Route::get('dps', [DPSApiController::class, 'getDocumentsInRange']);
    Route::get('dps-by-pk/', [DPSApiController::class, 'getDocument']);
    Route::post('dps/authorize-dps', [DPSApiController::class, 'authorizeDps']);
    Route::post('dps/reject-dps', [DPSApiController::class, 'rejectDps']);
});

Route::post('send-push-notification', [PushNotificationController::class, 'sendNotification']);
Route::post('send-push-notification-u', [PushNotificationController::class, 'sendGenericNotification']);
Route::post('send-push-notification-external', [PushNotificationController::class, 'sendExternalGenericNotification']);