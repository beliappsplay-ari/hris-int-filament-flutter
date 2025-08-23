<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PayrollApiController;
use App\Http\Controllers\Api\TimesheetController; // TIMESHEET CONTROLLER IMPORT
use App\Helpers\PdfHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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

// Debug routes untuk testing - REMOVE SETELAH TESTING
Route::get('/debug/timesheet-pdf/{period}', [TimesheetController::class, 'debugPdfExtraction']);
Route::get('/debug/timesheet-extract/{period}', [TimesheetController::class, 'extractPagePdf']);

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

    // REGISTER route - SAFE IMPLEMENTATION (replace existing register route)
    Route::post('/register', function (Request $request) {
        try {
            // Validation - tambah empno field ke existing validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'empno' => 'sometimes|string|min:3|max:50', // Optional empno
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user - SAMA seperti existing
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'empno' => $request->empno, // TAMBAH: Direct empno field
            ]);

            $employeeCreated = false;
            $employeeInfo = null;

            // TAMBAH: Create employee record jika empno diberikan
            if ($request->filled('empno')) {
                try {
                    // Check if empno already exists
                    $empnoExists = DB::table('emp_masters')
                        ->where('empno', strtoupper($request->empno))
                        ->exists();
                    
                    if ($empnoExists) {
                        // Empno sudah ada, kembalikan error
                        $user->delete(); // Rollback user creation
                        return response()->json([
                            'success' => false,
                            'message' => 'Employee number already exists',
                            'errors' => ['empno' => ['This employee number is already registered']]
                        ], 422);
                    }
                    
                    // Create employee record
                    DB::table('emp_masters')->insert([
                        'empno' => strtoupper($request->empno),
                        'fullname' => $request->name,
                        'user_id' => $user->id, // Link to user
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $employeeCreated = true;
                    $employeeInfo = [
                        'empno' => strtoupper($request->empno),
                        'fullname' => $request->name,
                    ];
                    
                    \Log::info('Employee record created', [
                        'user_id' => $user->id,
                        'empno' => strtoupper($request->empno)
                    ]);
                    
                } catch (\Exception $e) {
                    // Jika employee creation gagal, rollback user
                    $user->delete();
                    \Log::error('Employee creation failed', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'empno' => $request->empno
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create employee record: ' . $e->getMessage(),
                    ], 500);
                }
            }

            // Generate token - SAMA seperti existing
            $token = $user->createToken('authToken')->plainTextToken;

            // Response data
            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'empno' => $user->empno, // TAMBAH: Include empno
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
            ];

            // Tambah employee info jika berhasil dibuat
            if ($employeeCreated && $employeeInfo) {
                $responseData['employee'] = $employeeInfo;
            }

            $message = $employeeCreated 
                ? 'Registration successful! User and employee record created.'
                : 'Registration successful!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
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
    
    // FORGOT PASSWORD route
    Route::post('/forgot-password', function (Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this email not found'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password),
                'updated_at' => now(),
            ]);

            // Log password reset
            \Log::info('Password reset for user: ' . $user->email);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully! You can now login with your new password.',
                'data' => [
                    'email' => $user->email,
                    'reset_at' => now()->toDateTimeString()
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed: ' . $e->getMessage(),
            ], 500);
        }
    });

    // RESET PASSWORD route (alternative endpoint)
    Route::post('/reset-password', function (Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'new_password' => 'sometimes|string|min:6', // Alternative field
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this email not found'
                ], 404);
            }

            // Use new_password if provided, otherwise use password
            $newPassword = $request->new_password ?? $request->password;

            // Update password
            $user->update([
                'password' => Hash::make($newPassword),
                'updated_at' => now(),
            ]);

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            // Log password reset
            \Log::info('Password reset for user: ' . $user->email . ' via reset-password endpoint');

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully! Please login again with your new password.',
                'data' => [
                    'email' => $user->email,
                    'reset_at' => now()->toDateTimeString(),
                    'tokens_revoked' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed: ' . $e->getMessage(),
            ], 500);
        }
    });
    
    // Protected routes - MIDDLEWARE AUTH SANCTUM
    Route::group(['middleware' => 'auth:sanctum'], function () {
        
        // SALARY SLIPS - FOR FLUTTER COMPATIBILITY (FIXED)
        Route::get('/salary-slips', function (Request $request) {
            try {
                $user = $request->user();
                
                // FIXED: Try multiple ways to get employee number
                $empno = null;
                
                // Method 1: Direct empno field di users table
                if (isset($user->empno) && !empty($user->empno)) {
                    $empno = $user->empno;
                }
                // Method 2: Through employee relationship
                else {
                    try {
                        $employee = $user->employee;
                        if ($employee && isset($employee->empno)) {
                            $empno = $employee->empno;
                        }
                    } catch (\Exception $e) {
                        \Log::info('Employee relation not available: ' . $e->getMessage());
                    }
                }
                
                if (!$empno) {
                    return response()->json([
                        'success' => true,
                        'data' => [],
                        'message' => 'No employee number found for this user',
                        'debug' => [
                            'user_id' => $user->id,
                            'has_empno_field' => isset($user->empno),
                            'empno_value' => $user->empno ?? null,
                            'suggestion' => 'User needs empno field or employee relation'
                        ]
                    ]);
                }
                
                // Query dengan JOIN untuk get salary slips
                $payrolls = DB::select("
                    SELECT 
                        p.id,
                        p.empno,
                        p.period,
                        p.basicsalary,
                        p.total,
                        e.fullname
                    FROM payrolls p
                    LEFT JOIN emp_masters e ON p.empno = e.empno
                    WHERE p.empno = ?
                    ORDER BY p.period DESC
                ", [$empno]);
                
                $result = collect($payrolls)->map(function ($payroll) {
                    return [
                        'id' => $payroll->id,
                        'empno' => $payroll->empno,
                        'period' => $payroll->period,
                        'period_formatted' => PdfHelper::formatPeriodForDisplay($payroll->period),
                        'fullname' => $payroll->fullname ?? 'Unknown',
                        'basic_salary' => $payroll->basicsalary,
                        'total' => $payroll->total,
                        'pdf_url' => url("/api/payroll/view-pdf/{$payroll->empno}/{$payroll->period}"),
                        'has_pdf' => PdfHelper::checkPdfExists($payroll->empno, $payroll->period)
                    ];
                });
                
                return response()->json([
                    'success' => true,
                    'data' => $result,
                    'meta' => [
                        'total' => $result->count(),
                        'employee_name' => $result->isNotEmpty() ? $result->first()['fullname'] : $user->name,
                        'employee_no' => $empno,
                        'method' => isset($user->empno) ? 'direct_empno' : 'employee_relation'
                    ]
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Salary slips error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching salary slips: ' . $e->getMessage()
                ], 500);
            }
        });
        
        Route::get('/flutter/my-slips', function (Request $request) {
            try {
                $user = $request->user();
                
                // FIXED: Try multiple ways to get employee number (same as salary-slips)
                $empno = null;
                
                // Method 1: Direct empno field di users table
                if (isset($user->empno) && !empty($user->empno)) {
                    $empno = $user->empno;
                }
                // Method 2: Through employee relationship
                else {
                    try {
                        $employee = $user->employee;
                        if ($employee && isset($employee->empno)) {
                            $empno = $employee->empno;
                        }
                    } catch (\Exception $e) {
                        \Log::info('Employee relation not available: ' . $e->getMessage());
                    }
                }
                
                if (!$empno) {
                    return response()->json([
                        'success' => true,
                        'data' => [],
                        'message' => 'No employee number found for this user',
                        'debug' => [
                            'user_id' => $user->id,
                            'has_empno_field' => isset($user->empno),
                            'empno_value' => $user->empno ?? null
                        ]
                    ]);
                }
                
                // Query dengan JOIN tanpa merubah model existing
                $payrolls = DB::select("
                    SELECT 
                        p.id,
                        p.empno,
                        p.period,
                        p.basicsalary,
                        p.total,
                        e.fullname
                    FROM payrolls p
                    LEFT JOIN emp_masters e ON p.empno = e.empno
                    WHERE p.empno = ?
                    ORDER BY p.period DESC
                ", [$empno]);
                
                $result = collect($payrolls)->map(function ($payroll) {
                    return [
                        'id' => $payroll->id,
                        'empno' => $payroll->empno,
                        'period' => $payroll->period,
                        'period_formatted' => PdfHelper::formatPeriodForDisplay($payroll->period),
                        'fullname' => $payroll->fullname ?? 'Unknown',
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
                    'data' => $result,
                    'meta' => [
                        'total' => $result->count(),
                        'employee_name' => $result->isNotEmpty() ? $result->first()['fullname'] : $user->name,
                        'employee_no' => $empno,
                        'method' => isset($user->empno) ? 'direct_empno' : 'employee_relation'
                    ]
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching salary slips: ' . $e->getMessage()
                ], 500);
            }
        });

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
            
            // Get employee's salary slips list (FIXED)
            Route::get('/my-slips', function (Request $request) {
                try {
                    $user = $request->user();
                    
                    // FIXED: Try multiple ways to get employee number (same as above)
                    $empno = null;
                    
                    // Method 1: Direct empno field di users table
                    if (isset($user->empno) && !empty($user->empno)) {
                        $empno = $user->empno;
                    }
                    // Method 2: Through employee relationship
                    else {
                        try {
                            $employee = $user->employee;
                            if ($employee && isset($employee->empno)) {
                                $empno = $employee->empno;
                            }
                        } catch (\Exception $e) {
                            \Log::info('Employee relation not available: ' . $e->getMessage());
                        }
                    }
                    
                    if (!$empno) {
                        return response()->json([
                            'success' => true,
                            'data' => [],
                            'message' => 'No employee number found for this user'
                        ]);
                    }
                    
                    // Try using Eloquent model first, fallback to raw query
                    try {
                        $payrolls = \App\Models\Payroll::where('empno', $empno)
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
                    } catch (\Exception $e) {
                        // Fallback to raw query if Eloquent fails
                        \Log::info('Eloquent failed, using raw query: ' . $e->getMessage());
                        
                        $payrolls = DB::select("
                            SELECT 
                                p.id,
                                p.empno,
                                p.period,
                                p.basicsalary,
                                p.total,
                                COALESCE(e.fullname, p.fullname) as fullname
                            FROM payrolls p
                            LEFT JOIN emp_masters e ON p.empno = e.empno
                            WHERE p.empno = ?
                            ORDER BY p.period DESC
                        ", [$empno]);
                        
                        $payrolls = collect($payrolls)->map(function ($payroll) {
                            return [
                                'id' => $payroll->id,
                                'empno' => $payroll->empno,
                                'period' => $payroll->period,
                                'period_formatted' => PdfHelper::formatPeriodForDisplay($payroll->period),
                                'fullname' => $payroll->fullname ?? 'Unknown',
                                'basic_salary' => $payroll->basicsalary,
                                'total' => $payroll->total,
                                'pdf_url' => route('api.payroll.view.pdf', [
                                    'empno' => $payroll->empno,
                                    'period' => $payroll->period
                                ]),
                                'has_pdf' => PdfHelper::checkPdfExists($payroll->empno, $payroll->period)
                            ];
                        });
                    }
                    
                    return response()->json([
                        'success' => true,
                        'data' => $payrolls,
                        'meta' => [
                            'total' => $payrolls->count(),
                            'employee_name' => $payrolls->isNotEmpty() ? $payrolls->first()['fullname'] : $user->name,
                            'employee_no' => $empno,
                            'method' => isset($user->empno) ? 'direct_empno' : 'employee_relation'
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

        // ============================================================================
        // TIMESHEET ROUTES - UPDATED untuk public/assets/timesheet
        // ============================================================================
        
        Route::prefix('timesheet')->group(function () {
            
            // ========================================
            // MAIN TIMESHEET ENDPOINTS
            // ========================================
            
            /**
             * Get available timesheet periods
             * GET /api/timesheet/periods
             */
            Route::get('/periods', [TimesheetController::class, 'getAvailablePeriods']);
            
            /**
             * ✅ NEW: Get available timesheet periods dengan filter support
             * GET /api/timesheet/periods?empno=EMP001&period=202401
             */
            Route::get('/periods-filtered', [TimesheetController::class, 'getAvailablePeriodsFiltered']);
            
            /**
             * ✅ NEW: Get list of employees yang memiliki timesheet
             * GET /api/timesheet/employees
             */
            Route::get('/employees', [TimesheetController::class, 'getTimesheetEmployees']);
            
            /**
             * Extract employee page from timesheet PDF
             * GET /api/timesheet/extract-page/{period}
             */
            Route::get('/extract-page/{period}', [TimesheetController::class, 'extractEmployeePage'])
                 ->where('period', '[0-9]{6}')
                 ->name('api.timesheet.extract-page');
            
            /**
             * ✅ NEW: Extract employee page dengan empno filter support
             * GET /api/timesheet/extract-page-filtered/{period}?empno=EMP001
             */
            Route::get('/extract-page-filtered/{period}', [TimesheetController::class, 'extractEmployeePageWithFilter'])
                 ->where('period', '[0-9]{6}')
                 ->name('api.timesheet.extract-page-filtered');
            
            /**
             * Debug PDF extraction for testing
             * GET /api/timesheet/debug-pdf/{period}
             */
            Route::get('/debug-pdf/{period}', [TimesheetController::class, 'debugPdfExtraction'])
                 ->where('period', '[0-9]{6}')
                 ->name('api.timesheet.debug-pdf');
            
            /**
             * Get timesheet info by period dengan current user empno
             * GET /api/timesheet/period/{period}
             */
            Route::get('/period/{period}', [TimesheetController::class, 'getTimesheetByPeriod'])
                 ->where('period', '[0-9]{6}');
            
            /**
             * Check if timesheet PDF exists for period
             * GET /api/timesheet/check-pdf/{period}
             */
            Route::get('/check-pdf/{period}', [TimesheetController::class, 'checkPdfExists'])
                 ->where('period', '[0-9]{6}');
            
            // ✅ TAMBAHAN: Download PDF langsung
            Route::get('/download/{period}', [TimesheetController::class, 'downloadPdf'])
                 ->where('period', '[0-9]{6}');
            
            // ✅ TAMBAHAN: View PDF langsung
            Route::get('/view/{period}', [TimesheetController::class, 'viewPdf'])
                 ->where('period', '[0-9]{6}');
            
            // ========================================
            // DEBUG & SYSTEM ENDPOINTS
            // ========================================
            
            /**
             * System status
             * GET /api/timesheet/system/status
             */
            Route::get('/system/status', [TimesheetController::class, 'getSystemStatus']);
            
            /**
             * Debug current user empno dan timesheet access
             * GET /api/timesheet/debug/user
             */
            Route::get('/debug/user', [TimesheetController::class, 'debugUser']);
            
            /**
             * ✅ TAMBAHAN: Debug period mapping
             * GET /api/timesheet/debug/mapping
             */
            Route::get('/debug/mapping', [TimesheetController::class, 'debugPeriodMapping']);
            
            /**
             * Debug: List all files in timesheet directory
             * GET /api/timesheet/debug/files
             */
            Route::get('/debug/files', function () {
                // ✅ UPDATE: Prioritas public/assets/timesheet
                $possiblePaths = [
                    public_path('assets/timesheet'),        // ✅ PRIORITAS UTAMA
                    storage_path('app/public/pdf/timesheet'),
                    storage_path('app/pdf/timesheet'),
                    public_path('pdf/timesheet'),
                    storage_path('app/public/timesheet')
                ];
                
                $timesheetPath = null;
                foreach ($possiblePaths as $path) {
                    if (is_dir($path)) {
                        $timesheetPath = $path;
                        break;
                    }
                }
                
                if (!$timesheetPath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Timesheet folder not found',
                        'checked_paths' => $possiblePaths,
                        'suggestion' => 'Create folder: public/assets/timesheet and upload PDF files'
                    ]);
                }
                
                $files = array_values(array_diff(scandir($timesheetPath), ['.', '..']));
                $pdfFiles = array_filter($files, function($file) {
                    return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
                });
                
                $periodsFound = [];
                foreach ($pdfFiles as $file) {
                    if (preg_match('/^(\d{6})\.pdf$/', $file, $matches)) {
                        $period = $matches[1];
                        $filePath = $timesheetPath . '/' . $file;
                        $periodsFound[] = [
                            'period' => $period,
                            'filename' => $file,
                            'file_size_mb' => round(filesize($filePath) / 1024 / 1024, 2),
                            'formatted' => \App\Http\Controllers\Api\TimesheetController::formatPeriodDisplay($period),
                            // ✅ TAMBAHAN: Mapping info
                            'period_mapping' => [
                                'year' => substr($period, 0, 4),
                                'month' => substr($period, 4, 2),
                                'example' => $file . ' → ' . \App\Http\Controllers\Api\TimesheetController::formatPeriodDisplay($period)
                            ]
                        ];
                    }
                }
                
                // Get current user empno for context
                $user = Auth::user();
                $empno = null;
                if ($user) {
                    if (isset($user->empno)) {
                        $empno = $user->empno;
                    } elseif (method_exists($user, 'employee') && $user->employee) {
                        $empno = $user->employee->empno ?? null;
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'folder_path' => $timesheetPath,
                        'primary_path' => public_path('assets/timesheet'),
                        'path_used' => $timesheetPath,
                        'all_files' => $files,
                        'pdf_files' => array_values($pdfFiles),
                        'pdf_count' => count($pdfFiles),
                        'periods_found' => $periodsFound,
                        'expected_format' => 'YYYYMM.pdf (e.g., 202507.pdf for July 2025)',
                        // ✅ TAMBAHAN: Mapping explanation
                        'mapping_explanation' => [
                            'format' => 'YYYYMM.pdf',
                            'description' => 'Filename format: Year (4 digits) + Month (2 digits) + .pdf extension',
                            'examples' => [
                                '202507.pdf' => 'July 2025 (2025 + 07)',
                                '202506.pdf' => 'June 2025 (2025 + 06)',
                                '202508.pdf' => 'August 2025 (2025 + 08)',
                                '202412.pdf' => 'December 2024 (2024 + 12)'
                            ]
                        ],
                        'current_user' => [
                            'user_id' => $user ? $user->id : null,
                            'empno' => $empno,
                            'has_empno' => !is_null($empno),
                            'can_access_timesheet' => !is_null($empno)
                        ],
                        'access_method' => 'Direct empno from login → Select period → Auto-navigate',
                        'total_employees_mapped' => 29,
                        'example_access' => $empno ? [
                            'your_empno' => $empno,
                            'example_url' => url("/api/timesheet/extract-page/202507"),
                            'instruction' => "Login dengan empno {$empno} → Pilih period → Lihat PDF halaman Anda"
                        ] : [
                            'message' => 'Login dengan empno untuk akses timesheet',
                            'example_empno' => '22971',
                            'example_name' => 'ARI RAHMADI'
                        ]
                    ]
                ]);
            });
            
            // ========================================
            // LEGACY COMPATIBILITY
            // ========================================
            
            /**
             * Legacy endpoint for compatibility (no auth isolation)
             * GET /api/timesheet/view-pdf/{period}
             */
            Route::get('/view-pdf/{period}', function ($period) {
                // ✅ UPDATE: Prioritas public/assets/timesheet
                $possiblePaths = [
                    public_path("assets/timesheet/{$period}.pdf"),        // ✅ PRIORITAS UTAMA
                    storage_path("app/public/pdf/timesheet/{$period}.pdf"),
                    storage_path("app/pdf/timesheet/{$period}.pdf"),
                    public_path("pdf/timesheet/{$period}.pdf"),
                    storage_path("app/public/timesheet/{$period}.pdf")
                ];
                
                $pdfPath = null;
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $pdfPath = $path;
                        break;
                    }
                }
                
                if (!$pdfPath) {
                    return response()->json([
                        'error' => 'PDF not found',
                        'period' => $period,
                        'expected_filename' => $period . '.pdf',
                        'checked_paths' => $possiblePaths,
                        'period_formatted' => \App\Http\Controllers\Api\TimesheetController::formatPeriodDisplay($period),
                        'suggestion' => 'Upload ' . $period . '.pdf to public/assets/timesheet/'
                    ], 404);
                }
                
                return response(file_get_contents($pdfPath))
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="timesheet_legacy_' . $period . '.pdf"')
                    ->header('X-PDF-Type', 'legacy-full-pdf')
                    ->header('X-Method', 'backwards-compatibility')
                    ->header('X-Warning', 'Use extract-page for employee-specific access')
                    ->header('X-Period-Formatted', \App\Http\Controllers\Api\TimesheetController::formatPeriodDisplay($period));
            })->where('period', '[0-9]{6}');
            
        }); // End timesheet group
        
    }); // End auth:sanctum group
    
}); // End api group