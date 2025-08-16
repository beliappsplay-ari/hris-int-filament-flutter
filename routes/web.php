<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| API Routes - Integrated with Laravel Auth
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api'], function () {
    
    // Test route
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Laravel API is working!',
            'timestamp' => now(),
            'database' => 'Connected to: ' . config('database.default')
        ]);
    });
    
    // Login route tanpa CSRF check
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Hapus token lama
            $user->tokens()->delete();
            
            // Buat token baru
            $token = $user->createToken('authToken')->plainTextToken;

            // Load employee data jika ada
            $user->load('employee');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/me', function (Request $request) {
            $user = $request->user()->load('employee');
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        });
        
        Route::post('/logout', function (Request $request) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        });
        
        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user()->load('employee')
            ]);
        });
    });
});

// Import Auth facade
use Illuminate\Support\Facades\Auth;