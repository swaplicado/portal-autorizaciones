<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\RequisitionsController;

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

    /** requisiciones */
    Route::group(['as' => 'requisitions.'], function () {
        Route::get('/requisitions', [RequisitionsController::class, 'index'])->name('index');
        Route::post('/approbeResource', [RequisitionsController::class, 'approbeResource'])->name('approbe');
        Route::post('/rejectResource', [RequisitionsController::class, 'rejectResource'])->name('reject');
        Route::post('/getSteps', [RequisitionsController::class, 'getSteps'])->name('steps');
        Route::post('/getRows', [RequisitionsController::class, 'getRows'])->name('rows');
    });

});

Route::get('/unauthorized', function () {
    return view('layouts.unauthorized');
})->name('unauthorized');