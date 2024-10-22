<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DevController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/')->group(function () {
    Route::view('','welcome');
    Route::get('login', [AuthController::class, 'showLoginPage'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'permission'])->group(function () {
    Route::get('/dev', [DevController::class, 'index'])->name('dev.index');
    Route::post('/dev/execute', [DevController::class, 'executeSQL'])->name('dev.execute');
    Route::get('/dev/export/{format}', [DevController::class, 'export'])->name('dev.export');
});
