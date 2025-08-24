<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\TimesheetController;
use App\Http\Controllers\Api\PayrollApiController;

/*
|--------------------------------------------------------------------------
| Flutter Mobile API Routes
|--------------------------------------------------------------------------
| Route khusus untuk aplikasi Flutter mobile app
| Tidak mengubah existing web routes atau API routes
|--------------------------------------------------------------------------
*/

// Test endpoint untuk Flutter
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Flutter API endpoint is working!',
        'timestamp' => now(),
        'version' => 'v1.0'
    ]);
});

// Flutter Menu Access endpoint
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/menu-access', [TimesheetController::class, 'getUserMenuAccess']);
    
    // User profile endpoint 
    Route::get('/me', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });
    
    // Salary Slips endpoints for Flutter
    Route::get('/salary-slips', [PayrollApiController::class, 'index']);
    Route::get('/salary-slips/{id}', [PayrollApiController::class, 'show']);
    Route::get('/salary-slips/{id}/pdf', [PayrollApiController::class, 'viewPdf']);
    Route::get('/salary-slips/{id}/download', [PayrollApiController::class, 'downloadPdf']);
    
    // Timesheet endpoints for Flutter
    Route::get('/timesheet', [TimesheetController::class, 'getAvailablePeriods']);
    Route::get('/timesheet/{period}', [TimesheetController::class, 'getTimesheetByPeriod']);
    Route::get('/timesheet/{period}/pdf', [TimesheetController::class, 'extractEmployeePage']);
    Route::get('/timesheet/{period}/view', [TimesheetController::class, 'viewPdf']);
});

// Flutter Authentication Routes
Route::post('/login', function (\Illuminate\Http\Request $request) {
    try {
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->tokens()->delete(); // Delete previous tokens
            $token = $user->createToken('flutterAuthToken')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
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
