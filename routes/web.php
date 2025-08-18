<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PayrollApiController;
use App\Helpers\PdfHelper;
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

// Route untuk WEB - tanpa auth (untuk Filament admin)
Route::get('/payroll/view-pdf/{empno}/{period}', function ($empno, $period) {
    return PdfHelper::viewPdfSlip($empno, $period, false);
})->name('payroll.view.pdf');

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

    // Register route
    Route::post('/register', function (Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'empno' => 'required|string|max:50|unique:employees,empno',
            ]);

            // Create user
            $user = \App\Models\User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => \Hash::make($validatedData['password']),
            ]);

            // Create employee record
            $employee = \App\Models\Employee::create([
                'empno' => $validatedData['empno'],
                'fullname' => $validatedData['name'],
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please login with your credentials.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'employee' => [
                        'id' => $employee->id,
                        'empno' => $employee->empno,
                        'fullname' => $employee->fullname,
                    ]
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    });
    
    // Protected routes - MIDDLEWARE AUTH SANCTUM
    Route::group(['middleware' => 'auth:sanctum'], function () {
        
        // ME endpoint
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
        
        // Salary slips API routes
        Route::prefix('salary-slips')->group(function () {
            Route::get('/', [PayrollApiController::class, 'index']);
            Route::get('/{id}', [PayrollApiController::class, 'show']);
            Route::get('/{id}/pdf', [PayrollApiController::class, 'downloadPdf'])->name('api.salary-slips.pdf');
        });

        // Payroll routes untuk Flutter
        Route::prefix('payroll')->group(function () {
            
            // Route untuk API/Flutter - dengan auth (URL: /api/payroll/view-pdf/...)
            Route::get('/view-pdf/{empno}/{period}', function ($empno, $period) {
                return PdfHelper::viewPdfSlip($empno, $period, true);
            })->name('api.payroll.view.pdf');
            
            // Get employee's salary slips list
            Route::get('/my-slips', function (Request $request) {
                try {
                    $user = $request->user();
                    $employee = $user->employee;
                    
                    if (!$employee) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Employee record not found'
                        ], 404);
                    }
                    
                    $payrolls = \App\Models\Payroll::where('empno', $employee->empno)
                        ->orderBy('period', 'desc')
                        ->get(['id', 'empno', 'period', 'fullname', 'basicsalary', 'total'])
                        ->map(function ($payroll) {
                            return [
                                'id' => $payroll->id,
                                'empno' => $payroll->empno,
                                'period' => $payroll->period,
                                'period_formatted' => PdfHelper::formatPeriodForDisplay($payroll->period),
                                'fullname' => $payroll->fullname,
                                'basic_salary' => $payroll->basicsalary,
                                'total' => $payroll->total,
                                'pdf_url' => route('api.payroll.view.pdf', [
                                    'empno' => $payroll->empno,
                                    'period' => $payroll->period
                                ]),
                                'has_pdf' => PdfHelper::checkPdfExists($payroll->empno, $payroll->period)
                            ];
                        });
                    
                    return response()->json([
                        'success' => true,
                        'data' => $payrolls,
                        'meta' => [
                            'total' => $payrolls->count(),
                            'employee_name' => $employee->fullname,
                            'employee_no' => $employee->empno
                        ]
                    ]);
                    
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error fetching salary slips: ' . $e->getMessage()
                    ], 500);
                }
            });
            
            // Check if PDF exists for specific period
            Route::get('/check-pdf/{empno}/{period}', function ($empno, $period) {
                $exists = PdfHelper::checkPdfExists($empno, $period);
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'empno' => $empno,
                        'period' => $period,
                        'pdf_exists' => $exists,
                        'pdf_url' => $exists ? route('api.payroll.view.pdf', compact('empno', 'period')) : null
                    ]
                ]);
            });
        });

        // Logout route
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