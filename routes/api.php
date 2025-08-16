<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Endpoint untuk login. Tidak memerlukan otentikasi.
Route::post('/login', [AuthController::class, 'login']);

// Endpoint yang dilindungi oleh middleware 'auth:sanctum'.
// Hanya bisa diakses oleh user yang sudah login dan punya token.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
       return $request->user()->load('employee'); // Muat data employee
    });
    // Tambahkan endpoint lain yang butuh otentikasi di sini
    // Route::get('/profile', [UserProfileController::class, 'show']);
});