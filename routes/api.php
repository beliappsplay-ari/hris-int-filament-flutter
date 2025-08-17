<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\emp_master;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Laravel API is working!',
        'timestamp' => now(),
        'version' => app()->version()
    ]);
});

// Public routes
Route::post('/login', function (Request $request) {
    try {
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

            // Load employee data dengan safe loading
            try {
                $user->load('employee');
            } catch (\Exception $e) {
                \Log::info('Failed to load employee: ' . $e->getMessage());
            }

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
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Login error: ' . $e->getMessage()
        ], 500);
    }
});

// Protected routes dengan Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/me', function (Request $request) {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            try {
                $user->load('employee');
            } catch (\Exception $e) {
                \Log::info('Failed to load employee: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    });
    
    // EMPLOYEES endpoint - NEW
    Route::get('/employees', function (Request $request) {
        try {
            $employees = emp_master::with('user')->get();
            
            return response()->json([
                'success' => true,
                'data' => $employees,
                'count' => $employees->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load employees: ' . $e->getMessage()
            ], 500);
        }
    });
    
    Route::get('/user', function (Request $request) {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            try {
                $user->load('employee');
            } catch (\Exception $e) {
                \Log::info('Failed to load employee: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    });
    
    Route::post('/logout', function (Request $request) {
        try {
            $token = $request->user()->currentAccessToken();
            if ($token) {
                $token->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout error: ' . $e->getMessage()
            ], 500);
        }
    });
    
});