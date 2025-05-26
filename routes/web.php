<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\RequisitionsController;
use App\Http\Controllers\Pages\DPSController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Pages\RMController;
use App\Http\Controllers\UserManuals\userManualsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Auth::routes();

Route::middleware(['auth', 'app.middleware', 'menu'])->group( function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/manuals', [userManualsController::class, 'index'])->name('index');

    /** requisiciones */
    Route::group(['as' => 'requisitions.'], function () {
        Route::get('/requisitions', [RequisitionsController::class, 'index'])->name('index');
        Route::post('/approbeResource', [RequisitionsController::class, 'approbeResource'])->name('approbe');
        Route::post('/rejectResource', [RequisitionsController::class, 'rejectResource'])->name('reject');
        Route::post('/getSteps', [RequisitionsController::class, 'getSteps'])->name('steps');
        Route::post('/getRows', [RequisitionsController::class, 'getRows'])->name('rows');
    });
    /** dps */
    Route::group(['as' => 'dps.'], function () {
        Route::get('/dpsindex', [DPSController::class, 'index'])->name('index');
        Route::get('/dpspending', [DPSController::class, 'indexPending'])->name('pending');
        Route::get('/dps-range', [DPSController::class, 'getDocumentsInRange'])->name('dps-range');
        Route::get('/dps/{idyear}/{iddoc}', [DPSController::class, 'getDocument'])->name('by-pk');
        Route::post('/dps/authorize-dps/{idyear?}/{iddoc?}', [DPSController::class, 'authorizeDps'])->name('authorize-dps');
        Route::post('/dps/reject-dps/{idyear?}/{iddoc?}', [DPSController::class, 'rejectDps'])->name('reject-dps');
        Route::get('/dps/view/{idyear?}/{iddoc?}', [DPSController::class, 'view'])->name('view');
    });

    /** rm */
    Route::group(['as' => 'rm.'], function () {
        Route::get('/rmindex', [RMController::class, 'index'])->name('index');
        Route::get('/rmpending', [RMController::class, 'indexPending'])->name('pending');
        Route::get('/myrm', [RMController::class, 'indexMyRm'])->name('myrm');
        Route::get('/rm-range', [RMController::class, 'getDocumentsInRange'])->name('rm-range');
        Route::get('/rm/{iddoc}', [RMController::class, 'getDocument'])->name('by-pk');
        Route::post('/rm/authorize-rm/{iddoc?}', [RMController::class, 'authorizeRm'])->name('authorize-rm');
        Route::post('/rm/reject-rm/{iddoc?}', [RMController::class, 'rejectRm'])->name('reject-rm');
        Route::get('/rm/view/{idyear?}/{iddoc?}', [RMController::class, 'view'])->name('view');
    });
        

    Route::post('/save-subscription', function(Request $request) {
        $data = $request->all();

        try {
            auth()->user()->pushSubscriptions()->create([
                'endpoint' => $data['endpoint'],
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
            ]);
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => true]);
    });

    Route::get('/get-public-key', [NotificationsController::class, 'setVapidKeys'])->name('get-public-key');
});

Route::get('/send-notification', [NotificationsController::class, 'enviarNotificacion'])->name('send-notification');
Route::get('/send-notification-users', [NotificationsController::class, 'notificationByUser'])->name('send-notification-users');

Route::get('/unauthorized', function () {
    return view('layouts.unauthorized');
})->name('unauthorized');