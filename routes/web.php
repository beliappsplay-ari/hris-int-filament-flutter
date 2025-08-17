<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
//use App\Http\Controllers\EmpMasterController;
use App\Http\Controllers\Api\PayrollApiController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


// Route untuk Salary Slip PDF preview - buka di tab baru (menggunakan closure)
Route::get('/payroll/{id}/salary-slip', function ($id) {
    // Manual find payroll
    $payroll = \App\Models\Payroll::with(['employee', 'user'])->findOrFail($id);
    
    // Generate PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payrolls.salary-slip', ['payroll' => $payroll]);
    
    // Return PDF untuk preview di browser (inline)
    return response($pdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="salary-slip-' . $payroll->empno . '.pdf"');
})->name('payroll.salary.slip');

/*
|--------------------------------------------------------------------------
| Employee Management Routes
|--------------------------------------------------------------------------
*/
/*
Route::prefix('hris')->group(function () {
    Route::resource('emp-masters', EmpMasterController::class);
});
*/
/*
|--------------------------------------------------------------------------
| API Routes
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
    
    // Protected routes - MIDDLEWARE AUTH SANCTUM
    Route::group(['middleware' => 'auth:sanctum'], function () {
        
        // ME endpoint - PENTING!
        Route::get('/me', function (Request $request) {
            try {
                $user = $request->user();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found'
                    ], 401);
                }
                
                // Load employee data dengan safe loading
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
                    'message' => 'Error getting user: ' . $e->getMessage()
                ], 500);
            }
        });
        
        Route::prefix('salary-slips')->group(function () {
    Route::get('/', [PayrollApiController::class, 'index']);
    Route::get('/{id}', [PayrollApiController::class, 'show']);
    Route::get('/{id}/pdf', [PayrollApiController::class, 'downloadPdf'])->name('api.salary-slips.pdf');
        });



        Route::post('/logout', function (Request $request) {
            try {
                $request->user()->currentAccessToken()->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout error: ' . $e->getMessage()
                ], 500);
            }
        });
        
    });
});