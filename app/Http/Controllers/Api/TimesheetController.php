<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\emp_master;
use setasign\Fpdi\Fpdi;
use Exception;

class TimesheetController extends Controller
{
    // Employee to page mapping for 202507.pdf - LENGKAP SESUAI PDF
    private static $employeePageMapping = [
        '10132' => 1,   // YOVITA E. WAHYUNINGRUM - Page 1
        '22682' => 2,   // ALFA YUNITA SARASWATI - Page 2
        '22691' => 3,   // ARIS WAHYU HANANTO - Page 3
        '22783' => 4,   // SURADIMAN - Page 4
        '22785' => 5,   // IKA FARADINA - Page 5
        '22817' => 6,   // HENNI YULIASTARI - Page 6
        '22845' => 7,   // INDAH DARMAWATI - Page 7
        '22905' => 8,   // NILA FIRSTYA - Page 8
        '22928' => 9,   // FADILA YASMIN - Page 9
        '22938' => 10,  // IIN NURHAYATI - Page 10
        '22971' => 11,  // ARI RAHMADI - Page 11
        '22976' => 12,  // PUTRI KUSUMAWATI - Page 12
        '22988' => 13,  // RIZKY RIENALDI - Page 13
        '22989' => 14,  // ASMAH KHADAFIAH - Page 14
        '22997' => 15,  // RUWIATIN SUGIARTO - Page 15
        '23084' => 16,  // JONATHAN PAIDOTUA SIANTURI - Page 16
        '23089' => 17,  // ABDUL ROZAK MUMTA - Page 17
        '23095' => 18,  // REZKI ANGGRAEN - Page 18
        '23101' => 19,  // DANIEL HERRY WIBOWO - Page 19
        '23105' => 20,  // MUHAMMAD ALFIA RIZKI - Page 20
        '23106' => 21,  // HERRY SABARUDIN - Page 21
        '23119' => 22,  // MUHAMMAD ISMAIL HASIBUAN - Page 22
        '23123' => 23,  // BAGUS PUJIYANTO - Page 23
        '23126' => 24,  // SUKMA HASNAA AMBAROH - Page 24
        '23129' => 25,  // SITI ASPIYAH - Page 25
        '23131' => 26,  // GOLDY HERDANI RIVERO - Page 26
        '23132' => 27,  // ALVINA KHOIRUNIS - Page 27
        '23133' => 28,  // FITRIA ASANEGERI - Page 28
        '23135' => 29,  // KARINA AYU ALDRIAN - Page 29
    ];

    /**
     * SIMPLIFIED: Direct access - Get empno from user login
     * User sudah login dengan empno, langsung akses timesheet
     */
    public function extractEmployeePage(Request $request, $period)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'message' => 'Please login first'
                ], 401);
            }
            
            // SIMPLIFIED: Get empno langsung dari user login
            $empno = $this->getUserEmployeeNumber($user);
            
            if (!$empno) {
                return response()->json([
                    'error' => 'Employee number not found',
                    'message' => 'User does not have employee number. Please contact administrator.',
                    'debug' => [
                        'user_id' => $user->id,
                        'user_empno' => $user->empno ?? null,
                        'has_employee_relation' => isset($user->employee),
                        'suggestion' => 'Admin needs to set empno for this user'
                    ]
                ], 400);
            }
            
            // ✅ UPDATED: Get employee info dengan validasi database dan PDF mapping
            $employeeInfo = $this->getEmployeeInfo($empno);
            
            if (!$employeeInfo['timesheet_available']) {
                return response()->json([
                    'error' => 'Timesheet not available',
                    'message' => $employeeInfo['message'],
                    'employee_info' => [
                        'empno' => $empno,
                        'employee_name' => $employeeInfo['employee_name'],
                        'exists_in_database' => $employeeInfo['exists_in_database'],
                        'exists_in_pdf_mapping' => $employeeInfo['exists_in_pdf_mapping']
                    ],
                    'available_employees' => array_keys(self::$employeePageMapping),
                    'debug' => [
                        'requested_empno' => $empno,
                        'database_check' => $employeeInfo['exists_in_database'] ? 'Found' : 'Not found',
                        'pdf_mapping_check' => $employeeInfo['exists_in_pdf_mapping'] ? 'Available' : 'Not available',
                        'suggestion' => $employeeInfo['exists_in_database'] && !$employeeInfo['exists_in_pdf_mapping'] 
                            ? 'Employee exists in database but timesheet PDF page not mapped'
                            : 'Employee not found in database and timesheet not available'
                    ]
                ], 404);
            }
            
            $pageNumber = $employeeInfo['page_number'];
            $employeeName = $employeeInfo['employee_name'];
            
            // Find PDF file
            $sourcePdfPath = $this->findPdfFile($period);
            if (!$sourcePdfPath) {
                return response()->json([
                    'error' => 'Timesheet PDF not found',
                    'message' => "PDF file not found for period: {$period}",
                    'checked_paths' => $this->getPossiblePdfPaths($period)
                ], 404);
            }
            
            $fileSize = filesize($sourcePdfPath);
            $totalPages = $this->getPdfPageCount($sourcePdfPath);
            
            Log::info('Timesheet accessed', [
                'empno' => $empno,
                'employee_name' => $employeeName,
                'period' => $period,
                'page' => $pageNumber,
                'file_size' => $fileSize,
                'total_pages' => $totalPages,
                'user_id' => $user->id,
                'database_employee' => $employeeInfo['exists_in_database'],
                'pdf_mapping_available' => $employeeInfo['exists_in_pdf_mapping']
            ]);
            
            // ✅ EXTRACT SPECIFIC PAGE: Extract hanya halaman yang diperlukan
            $extractedPdfContent = $this->extractSinglePageFromPdf($sourcePdfPath, $pageNumber);
            
            if (!$extractedPdfContent) {
                return response()->json([
                    'error' => 'Page extraction failed',
                    'message' => "Failed to extract page {$pageNumber} from PDF",
                    'debug' => [
                        'source_pdf' => $sourcePdfPath,
                        'target_page' => $pageNumber,
                        'total_pages' => $totalPages
                    ]
                ], 500);
            }
            
            // Return extracted single page PDF
            return response($extractedPdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="timesheet_' . $empno . '_' . $period . '_page' . $pageNumber . '.pdf"')
                ->header('X-Employee-No', $empno)
                ->header('X-Target-Page', $pageNumber)
                ->header('X-Employee-Name', $employeeName)
                ->header('X-Total-Pages', 1) // ✅ Now only 1 page
                ->header('X-Original-Total-Pages', $totalPages)
                ->header('X-Extracted-Page', $pageNumber)
                ->header('X-File-Size-MB', round(strlen($extractedPdfContent) / 1024 / 1024, 2))
                ->header('X-Period', $period)
                ->header('X-Period-Formatted', self::formatPeriodDisplay($period))
                ->header('X-Extraction-Method', 'single-page-extract')
                ->header('X-Page-Navigation', 'page-' . $pageNumber . '-only')
                ->header('X-Instructions', 'PDF contains only page ' . $pageNumber . ' for ' . $employeeName)
                ->header('X-Security', 'employee-isolated-single-page')
                ->header('X-Employee-Database-Status', $employeeInfo['exists_in_database'] ? 'found' : 'not-found')
                ->header('X-Employee-Source', 'emp_masters-table-dynamic');
                
        } catch (\Exception $e) {
            Log::error('Timesheet extraction error: ' . $e->getMessage(), [
                'period' => $period,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to get timesheet',
                'message' => $e->getMessage(),
                'period' => $period
            ], 500);
        }
    }

    /**
     * SIMPLIFIED: Get empno langsung dari user login
     */
    private function getUserEmployeeNumber($user)
    {
        // Method 1: Direct empno field di users table
        if (isset($user->empno) && !empty($user->empno)) {
            return $user->empno;
        }
        
        // Method 2: Through employee relationship (emp_masters)
        try {
            // Access employee relation directly without load
            if ($user->employee && isset($user->employee->empno)) {
                return $user->employee->empno;
            }
        } catch (\Exception $e) {
            Log::info('Employee relation not available: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get available timesheet periods
     * ✅ UPDATED: Support period filter dan empno info
     */
    public function getAvailablePeriods(Request $request)
    {
        try {
            $user = Auth::user();
            $empno = $this->getUserEmployeeNumber($user);
            $filterPeriod = $request->query('period'); // Support period filter
            
            $timesheetPath = $this->findTimesheetDirectory();
            
            if (!$timesheetPath) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'message' => 'Timesheet directory not found',
                    'checked_paths' => $this->getPossibleTimesheetPaths()
                ]);
            }
            
            $files = array_diff(scandir($timesheetPath), ['.', '..']);
            $periods = [];
            
            foreach ($files as $file) {
                if (preg_match('/^(\d{6})\.pdf$/', $file, $matches)) {
                    $period = $matches[1];
                    
                    // ✅ Filter by period if specified
                    if ($filterPeriod && $period !== $filterPeriod) {
                        continue;
                    }
                    
                    $filePath = $timesheetPath . '/' . $file;
                    
                    $periods[] = [
                        'period' => $period,
                        'period_formatted' => self::formatPeriodDisplay($period),
                        'filename' => $file,
                        'file_size_mb' => round(filesize($filePath) / 1024 / 1024, 2),
                        'page_count' => $this->getPdfPageCount($filePath),
                        // ✅ TAMBAHAN: Employee info untuk current user
                        'employee_name' => $empno ? $this->getEmployeeName($empno) : 'Unknown',
                        'empno' => $empno ?? 'unknown',
                        'has_pdf' => true,
                        'created_at' => date('Y-m-d\TH:i:s\Z', filemtime($filePath)),
                        // ✅ TAMBAHAN: Mapping informasi
                        'period_mapping' => [
                            'year' => substr($period, 0, 4),
                            'month' => substr($period, 4, 2),
                            'month_name' => $this->getMonthName(substr($period, 4, 2)),
                            'full_name' => self::formatPeriodDisplay($period),
                            'filename_format' => 'YYYYMM.pdf',
                            'example' => $period . '.pdf → ' . self::formatPeriodDisplay($period)
                        ],
                        // ✅ TAMBAHAN: URL dan akses info
                        'access_info' => [
                            'pdf_url' => url("/api/timesheet/extract-page/{$period}"), // ✅ Use extract-page for user-specific access
                            'direct_download' => url("/api/timesheet/download/{$period}"),
                            'view_url' => url("/api/timesheet/view/{$period}")
                        ]
                    ];
                }
            }
            
            // Sort by period descending (most recent first)
            usort($periods, function($a, $b) {
                return $b['period'] <=> $a['period'];
            });
            
            return response()->json([
                'success' => true,
                'data' => $periods,
                'total' => count($periods),
                'directory' => $timesheetPath,
                'current_user' => [
                    'empno' => $empno,
                    'employee_name' => $empno ? $this->getEmployeeName($empno) : 'Unknown',
                    'has_timesheet_access' => !is_null($empno) && isset(self::$employeePageMapping[$empno])
                ],
                'filter_applied' => [
                    'period' => $filterPeriod,
                    'note' => 'Auto-filtered by current user empno: ' . ($empno ?? 'none')
                ],
                // ✅ TAMBAHAN: Mapping explanation
                'mapping_info' => [
                    'format' => 'YYYYMM.pdf',
                    'description' => 'Period format: Year (4 digits) + Month (2 digits) + .pdf extension',
                    'examples' => [
                        '202507.pdf' => 'July 2025',
                        '202506.pdf' => 'June 2025',
                        '202508.pdf' => 'August 2025',
                        '202412.pdf' => 'December 2024'
                    ],
                    'current_files' => array_map(function($p) {
                        return $p['filename'] . ' → ' . $p['period_formatted'];
                    }, $periods)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting periods: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting periods: ' . $e->getMessage()
            ], 500);
        }
    }

// ✅ TAMBAHAN: Method helper untuk get month name
private function getMonthName($monthNumber)
{
    $monthNames = [
        '01' => 'January', '02' => 'February', '03' => 'March',
        '04' => 'April', '05' => 'May', '06' => 'June',
        '07' => 'July', '08' => 'August', '09' => 'September',
        '10' => 'October', '11' => 'November', '12' => 'December'
    ];
    
    return $monthNames[$monthNumber] ?? "Month {$monthNumber}";
}

// ✅ TAMBAHAN: Method untuk download direct PDF
public function downloadPdf(Request $request, $period)
{
    try {
        $pdfPath = $this->findPdfFile($period);
        
        if (!$pdfPath) {
            return response()->json([
                'error' => 'PDF not found',
                'period' => $period,
                'filename_expected' => $period . '.pdf'
            ], 404);
        }
        
        $filename = "timesheet_{$period}_" . self::formatPeriodDisplay($period) . ".pdf";
        
        return response()->download($pdfPath, $filename, [
            'Content-Type' => 'application/pdf'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Download failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

// ✅ TAMBAHAN: Method untuk view PDF langsung
public function viewPdf(Request $request, $period)
{
    try {
        $pdfPath = $this->findPdfFile($period);
        
        if (!$pdfPath) {
            return response()->json([
                'error' => 'PDF not found',
                'period' => $period,
                'filename_expected' => $period . '.pdf',
                'period_formatted' => self::formatPeriodDisplay($period)
            ], 404);
        }
        
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="timesheet_' . $period . '.pdf"'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'View failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

// ✅ TAMBAHAN: Debug method untuk show semua mapping
public function debugPeriodMapping()
{
    try {
        $timesheetPath = $this->findTimesheetDirectory();
        $mappingInfo = [];
        
        if ($timesheetPath) {
            $files = array_diff(scandir($timesheetPath), ['.', '..']);
            
            foreach ($files as $file) {
                if (preg_match('/^(\d{6})\.pdf$/', $file, $matches)) {
                    $period = $matches[1];
                    $year = substr($period, 0, 4);
                    $month = substr($period, 4, 2);
                    
                    $mappingInfo[] = [
                        'filename' => $file,
                        'period_code' => $period,
                        'year' => $year,
                        'month' => $month,
                        'month_name' => $this->getMonthName($month),
                        'formatted_display' => self::formatPeriodDisplay($period),
                        'full_mapping' => $file . ' → ' . self::formatPeriodDisplay($period)
                    ];
                }
            }
        }
        
        return response()->json([
            'mapping_explanation' => [
                'format' => 'YYYYMM.pdf',
                'description' => 'Filename format uses Year (4 digits) + Month (2 digits) + .pdf extension',
                'breakdown' => [
                    'YYYY' => 'Year (e.g., 2025)',
                    'MM' => 'Month (01-12, with leading zero)',
                    '.pdf' => 'File extension'
                ]
            ],
            'examples' => [
                '202507.pdf' => 'July 2025 (2025 + 07)',
                '202506.pdf' => 'June 2025 (2025 + 06)',
                '202412.pdf' => 'December 2024 (2024 + 12)',
                '202501.pdf' => 'January 2025 (2025 + 01)'
            ],
            'current_files' => $mappingInfo,
            'total_files' => count($mappingInfo),
            'directory' => $timesheetPath
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug failed',
            'message' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Get timesheet by period with current user employee info
     */
    public function getTimesheetByPeriod(Request $request, $period)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }
            
            // Get empno dari user login
            $empno = $this->getUserEmployeeNumber($user);
            
            if (!$empno) {
                return response()->json([
                    'error' => 'Employee number not found for user',
                    'debug' => [
                        'user_id' => $user->id,
                        'suggestion' => 'User needs empno field or employee relation'
                    ]
                ], 400);
            }
            
            $pageNumber = self::$employeePageMapping[$empno] ?? null;
            
            // Find PDF file
            $pdfPath = $this->findPdfFile($period);
            $pdfExists = !is_null($pdfPath);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'period_formatted' => self::formatPeriodDisplay($period),
                    'empno' => $empno,
                    'employee_name' => $this->getEmployeeName($empno),
                    'page_number' => $pageNumber,
                    'pdf_exists' => $pdfExists,
                    'pdf_url' => $pdfExists ? url("/api/timesheet/extract-page/{$period}") : null,
                    'file_size_mb' => $pdfExists ? round(filesize($pdfPath) / 1024 / 1024, 2) : 0,
                    'total_pages' => $pdfExists ? $this->getPdfPageCount($pdfPath) : 0,
                    'instruction' => $pageNumber ? "PDF will auto-navigate to page {$pageNumber} for your timesheet." : "Page mapping not available for empno: {$empno}",
                    'method' => 'direct-empno-access'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting timesheet: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting timesheet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug current user info - SIMPLIFIED
     */
    public function debugUser(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'authenticated' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Try to access employee relation safely
        $employeeData = null;
        try {
            $employeeData = $user->employee;
        } catch (\Exception $e) {
            Log::info('Employee relation not available: ' . $e->getMessage());
        }
        
        $empno = $this->getUserEmployeeNumber($user);
        
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_empno_field' => $user->empno ?? null,
            'has_empno_field' => isset($user->empno) && !empty($user->empno),
            'has_employee_relation' => !is_null($employeeData),
            'employee_data' => $employeeData,
            'resolved_empno' => $empno,
            'has_timesheet_access' => !is_null($empno) && isset(self::$employeePageMapping[$empno]),
            'timesheet_page' => $empno ? (self::$employeePageMapping[$empno] ?? null) : null,
            'employee_name' => $empno ? $this->getEmployeeName($empno) : null,
            'total_employees_mapped' => count(self::$employeePageMapping),
            'available_periods' => $this->getAvailablePeriodsSimple(),
        ]);
    }

    /**
     * Helper: Get simple list of available periods
     */
    private function getAvailablePeriodsSimple()
    {
        try {
            $timesheetPath = $this->findTimesheetDirectory();
            if (!$timesheetPath) return [];
            
            $files = array_diff(scandir($timesheetPath), ['.', '..']);
            $periods = [];
            
            foreach ($files as $file) {
                if (preg_match('/^(\d{6})\.pdf$/', $file, $matches)) {
                    $periods[] = $matches[1];
                }
            }
            
            rsort($periods); // Most recent first
            return $periods;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * System status - SIMPLIFIED
     */
    public function getSystemStatus()
    {
        $timesheetPath = $this->findTimesheetDirectory();
        $pdfFiles = [];
        
        if ($timesheetPath) {
            $files = array_diff(scandir($timesheetPath), ['.', '..']);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                    $filePath = $timesheetPath . '/' . $file;
                    $pdfFiles[] = [
                        'filename' => $file,
                        'size_mb' => round(filesize($filePath) / 1024 / 1024, 2),
                        'pages' => $this->getPdfPageCount($filePath)
                    ];
                }
            }
        }
        
        return response()->json([
            'status' => 'operational',
            'mode' => 'direct-empno-access',
            'platform' => PHP_OS,
            'php_version' => PHP_VERSION,
            'pdf_extraction' => 'full-pdf-with-navigation-headers',
            'employee_mapping' => self::$employeePageMapping,
            'available_pdfs' => $pdfFiles,
            'timesheet_directory' => $timesheetPath,
            'current_user' => [
                'id' => Auth::id(),
                'authenticated' => Auth::check(),
                'empno' => Auth::user() ? $this->getUserEmployeeNumber(Auth::user()) : null
            ],
            'total_employees' => count(self::$employeePageMapping),
            'access_method' => 'Login with empno → Select period → Auto-navigate to employee page',
            'endpoints' => [
                'get_timesheet' => '/api/timesheet/extract-page/{period}',
                'periods' => '/api/timesheet/periods', 
                'timesheet_info' => '/api/timesheet/period/{period}',
                'debug_user' => '/api/timesheet/debug/user',
                'system_status' => '/api/timesheet/system/status'
            ]
        ]);
    }

    /**
     * Helper methods
     */
    
    private function findTimesheetDirectory()
    {
        $possiblePaths = $this->getPossibleTimesheetPaths();
        
        foreach ($possiblePaths as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    private function getPossibleTimesheetPaths()
    {
        return [
            public_path('assets/timesheet'),        // ✅ PRIORITAS UTAMA - SESUAI REQUEST
            storage_path('app/public/pdf/timesheet'),
            storage_path('app/pdf/timesheet'),
            public_path('pdf/timesheet'),
            storage_path('app/public/timesheet')
        ];
    }
    
    private function findPdfFile($period)
    {
        $possiblePaths = $this->getPossiblePdfPaths($period);
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    private function getPossiblePdfPaths($period)
    {
        return [
         public_path("assets/timesheet/{$period}.pdf"),        // ✅ PRIORITAS UTAMA - SESUAI REQUEST
            storage_path("app/public/pdf/timesheet/{$period}.pdf"),
            storage_path("app/pdf/timesheet/{$period}.pdf"),
            public_path("pdf/timesheet/{$period}.pdf"),
            storage_path("app/public/timesheet/{$period}.pdf")
        ];
    }

    /**
     * Get PDF page count using PHP-only method
     */
    private function getPdfPageCount($pdfPath)
    {
        try {
            $content = file_get_contents($pdfPath);
            
            // Method 1: Count /Type /Page objects (most reliable)
            if (preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches)) {
                $pageCount = count($matches[0]);
                if ($pageCount > 0) return $pageCount;
            }
            
            // Method 2: Look for /Count in page tree
            if (preg_match('/\/Count\s+(\d+)/', $content, $matches)) {
                return (int)$matches[1];
            }
            
            // Method 3: Estimation
            $estimatedPages = max(1, round(strlen($content) / 50000));
            return $estimatedPages;
            
        } catch (\Exception $e) {
            Log::error('Error counting PDF pages: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * ✅ NEW: Get employee name from emp_masters table (dynamic)
     * Falls back to default if not found in database
     */
    private function getEmployeeName($empno)
    {
        try {
            // Query emp_masters table untuk get fullname
            $employee = emp_master::where('empno', $empno)->first();
            
            if ($employee && !empty($employee->fullname)) {
                Log::info("Employee found in database", [
                    'empno' => $empno,
                    'fullname' => $employee->fullname
                ]);
                return $employee->fullname;
            }
            
            Log::warning("Employee not found in emp_masters table", [
                'empno' => $empno,
                'fallback' => "Employee {$empno}"
            ]);
            
            return "Employee {$empno}";
            
        } catch (\Exception $e) {
            Log::error("Error getting employee name from database: " . $e->getMessage(), [
                'empno' => $empno,
                'error' => $e->getMessage()
            ]);
            
            return "Employee {$empno}";
        }
    }

    /**
     * ✅ NEW: Check if employee exists in database
     */
    private function checkEmployeeExists($empno)
    {
        try {
            return emp_master::where('empno', $empno)->exists();
        } catch (\Exception $e) {
            Log::error("Error checking employee existence: " . $e->getMessage(), [
                'empno' => $empno
            ]);
            return false;
        }
    }

    /**
     * ✅ NEW: Get employee info dengan validasi database dan PDF mapping
     */
    private function getEmployeeInfo($empno)
    {
        $info = [
            'empno' => $empno,
            'exists_in_database' => $this->checkEmployeeExists($empno),
            'exists_in_pdf_mapping' => isset(self::$employeePageMapping[$empno]),
            'employee_name' => null,
            'page_number' => null,
            'timesheet_available' => false,
            'message' => ''
        ];

        // Get name from database
        if ($info['exists_in_database']) {
            $info['employee_name'] = $this->getEmployeeName($empno);
        } else {
            $info['employee_name'] = "Employee {$empno}";
            $info['message'] = "Employee {$empno} not found in employee database";
        }

        // Check PDF mapping
        if ($info['exists_in_pdf_mapping']) {
            $info['page_number'] = self::$employeePageMapping[$empno];
            $info['timesheet_available'] = true;
        } else {
            $info['message'] = $info['exists_in_database'] 
                ? "Timesheet not available for {$info['employee_name']} (empno: {$empno})"
                : "Employee {$empno} not found and timesheet not available";
        }

        return $info;
    }

    /**
     * Format period display (202507 -> July 2025)
     * Made static to support usage in routes
     */
    public static function formatPeriodDisplay($period)
    {
        if (!preg_match('/^(\d{4})(\d{2})$/', $period, $matches)) {
            return $period;
        }
        
        $year = $matches[1];
        $month = $matches[2];
        
        $monthNames = [
            '01' => 'January', '02' => 'February', '03' => 'March',
            '04' => 'April', '05' => 'May', '06' => 'June',
            '07' => 'July', '08' => 'August', '09' => 'September',
            '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        return ($monthNames[$month] ?? "Month $month") . " $year";
    }

    /**
     * Check if timesheet PDF exists for period
     */
    public function checkPdfExists($period)
    {
        $pdfPath = $this->findPdfFile($period);
        $exists = !is_null($pdfPath);
        
        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'pdf_exists' => $exists,
                'pdf_path' => $pdfPath,
                'checked_paths' => $this->getPossiblePdfPaths($period),
                'file_size_mb' => $exists ? round(filesize($pdfPath) / 1024 / 1024, 2) : 0,
                'total_pages' => $exists ? $this->getPdfPageCount($pdfPath) : 0,
                'extract_url' => $exists ? url("/api/timesheet/extract-page/{$period}") : null
            ]
        ]);
    }

    // ============================================================================
    // DEBUG METHOD FOR TESTING PDF EXTRACTION
    // ============================================================================
    
    /**
     * Debug PDF extraction - test FPDI functionality
     * GET /api/timesheet/debug-pdf/{period}
     */
    public function debugPdfExtraction($period)
    {
        try {
            // Default to empno 22971 (ARI RAHMADI) and page 11
            $empno = '22971';
            $page = 11;
            
            $sourcePdfPath = $this->findPdfFile($period);
            
            if (!$sourcePdfPath) {
                return response()->json([
                    'error' => 'PDF not found',
                    'period' => $period,
                    'paths_checked' => $this->getPossiblePdfPaths($period)
                ], 404);
            }
            
            $originalSize = filesize($sourcePdfPath);
            
            Log::info('Debug PDF extraction started', [
                'source_pdf' => $sourcePdfPath,
                'original_size' => $originalSize,
                'empno' => $empno,
                'target_page' => $page
            ]);
            
            // Test simple FPDI extraction without logging loop
            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($sourcePdfPath);
            $templateId = $pdf->importPage($page);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
            $extractedContent = $pdf->Output('S');
            
            $extractedSize = strlen($extractedContent);
            
            return response()->json([
                'success' => true,
                'empno' => $empno,
                'original_pdf_size' => $originalSize,
                'extracted_pdf_size' => $extractedSize,
                'compression_ratio' => round(($extractedSize / $originalSize) * 100, 2) . '%',
                'page_extracted' => $page,
                'total_pages' => $pageCount,
                'page_size' => $size,
                'message' => 'PDF extraction successful',
                'file_size_check' => $extractedSize > 1000 ? 'Good' : 'Too small'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Debug PDF extraction failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Debug extraction failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Extract and download single page PDF - for testing
     * GET /debug/timesheet-extract/{period}
     */
    public function extractPagePdf($period)
    {
        try {
            // Default to empno 22971 (ARI RAHMADI) and page 11
            $empno = '22971';
            $page = 11;
            
            $sourcePdfPath = $this->findPdfFile($period);
            
            if (!$sourcePdfPath) {
                return response()->json([
                    'error' => 'PDF not found',
                    'period' => $period,
                    'paths_checked' => $this->getPossiblePdfPaths($period)
                ], 404);
            }
            
            Log::info('Extracting single page for download', [
                'source_pdf' => $sourcePdfPath,
                'empno' => $empno,
                'target_page' => $page
            ]);
            
            // Extract single page using FPDI
            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($sourcePdfPath);
            $templateId = $pdf->importPage($page);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
            $extractedContent = $pdf->Output('S');
            
            $filename = "timesheet_{$empno}_{$period}_page{$page}.pdf";
            
            Log::info('PDF extraction completed', [
                'extracted_size' => strlen($extractedContent),
                'filename' => $filename
            ]);
            
            return response($extractedContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
            
        } catch (\Exception $e) {
            Log::error('PDF extraction download failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'PDF extraction failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: Test specific employee validation - for testing database integration
     * GET /debug/test-employee/{empno}
     */
    public function testEmployeeValidation($empno)
    {
        try {
            $employeeInfo = $this->getEmployeeInfo($empno);
            
            return response()->json([
                'success' => true,
                'empno' => $empno,
                'employee_info' => $employeeInfo,
                'test_results' => [
                    'database_check' => $employeeInfo['exists_in_database'] ? '✅ Found in emp_masters' : '❌ Not found in emp_masters',
                    'pdf_mapping_check' => $employeeInfo['exists_in_pdf_mapping'] ? '✅ Has timesheet page mapping' : '❌ No timesheet page mapping',
                    'timesheet_access' => $employeeInfo['timesheet_available'] ? '✅ Timesheet available' : '❌ Timesheet not available',
                    'final_status' => $employeeInfo['timesheet_available'] 
                        ? "User can access timesheet page {$employeeInfo['page_number']}"
                        : $employeeInfo['message']
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test employee validation failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Test failed',
                'message' => $e->getMessage(),
                'empno' => $empno
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get user menu access berdasarkan field akses di emp_masters
     * GET /api/user/menu-access
     */
    public function getUserMenuAccess(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'message' => 'Please login first'
                ], 401);
            }
            
            // Get empno dari user login
            $empno = $this->getUserEmployeeNumber($user);
            
            if (!$empno) {
                return response()->json([
                    'error' => 'Employee number not found for user',
                    'message' => 'User does not have employee number',
                    'debug' => [
                        'user_id' => $user->id,
                        'suggestion' => 'User needs empno field or employee relation'
                    ]
                ], 400);
            }
            
            // Get employee data dengan field akses
            $employee = emp_master::where('empno', $empno)->first();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'Employee not found in database',
                    'message' => "Employee with empno {$empno} not found in emp_masters table",
                    'empno' => $empno
                ], 404);
            }
            
            // Parse akses field (bisa berupa: "1", "2", "3", "1,2", "1,2,3", etc.)
            $accessString = $employee->akses ?? '';
            $accessArray = [];
            
            if (!empty($accessString)) {
                // Split by comma and clean up
                $accessArray = array_map('trim', explode(',', $accessString));
                $accessArray = array_filter($accessArray, function($value) {
                    return !empty($value) && is_numeric($value);
                });
                $accessArray = array_map('intval', $accessArray);
            }
            
            // Define menu mapping
            $menuMapping = [
                1 => [
                    'id' => 'salary_slip',
                    'title' => 'Salary Slips',
                    'subtitle' => 'View salary slips',
                    'icon' => 'cloud_download',
                    'color' => 'primary',
                    'route' => '/salary-slips',
                    'enabled' => true
                ],
                2 => [
                    'id' => 'timesheet',
                    'title' => 'Timesheet',
                    'subtitle' => 'View timesheet reports',
                    'icon' => 'schedule',
                    'color' => 'orange',
                    'route' => '/timesheet',
                    'enabled' => true
                ],
                3 => [
                    'id' => 'reports',
                    'title' => 'Reports',
                    'subtitle' => 'View analytics & reports',
                    'icon' => 'analytics',
                    'color' => 'purple',
                    'route' => '/reports',
                    'enabled' => true
                ]
            ];
            
            // Build accessible menus
            $accessibleMenus = [];
            foreach ($accessArray as $accessId) {
                if (isset($menuMapping[$accessId])) {
                    $accessibleMenus[] = $menuMapping[$accessId];
                }
            }
            
            // Always add default menus that don't require special access
            $defaultMenus = [
                [
                    'id' => 'employees',
                    'title' => 'Employees',
                    'subtitle' => 'Manage employee data',
                    'icon' => 'people',
                    'color' => 'blue',
                    'route' => '/employees',
                    'enabled' => false, // Coming soon
                    'access_required' => false
                ],
                [
                    'id' => 'settings',
                    'title' => 'Settings',
                    'subtitle' => 'App configuration',
                    'icon' => 'settings',
                    'color' => 'grey',
                    'route' => '/settings',
                    'enabled' => false, // Coming soon
                    'access_required' => false
                ],
                [
                    'id' => 'documents',
                    'title' => 'Documents',
                    'subtitle' => 'View documents',
                    'icon' => 'folder_open',
                    'color' => 'teal',
                    'route' => '/documents',
                    'enabled' => false, // Coming soon
                    'access_required' => false
                ]
            ];
            
            // Combine accessible menus with default menus
            $allMenus = array_merge($accessibleMenus, $defaultMenus);
            
            Log::info('User menu access retrieved', [
                'empno' => $empno,
                'employee_name' => $employee->fullname ?? 'Unknown',
                'access_string' => $accessString,
                'access_array' => $accessArray,
                'accessible_menus_count' => count($accessibleMenus),
                'total_menus' => count($allMenus)
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'employee_info' => [
                        'empno' => $empno,
                        'fullname' => $employee->fullname ?? 'Unknown',
                        'access_string' => $accessString,
                        'access_array' => $accessArray
                    ],
                    'menus' => $allMenus,
                    'accessible_menus' => $accessibleMenus,
                    'default_menus' => $defaultMenus
                ],
                'total_menus' => count($allMenus),
                'accessible_count' => count($accessibleMenus),
                'access_explanation' => [
                    '1' => 'Salary Slips access',
                    '2' => 'Timesheet access', 
                    '3' => 'Reports access',
                    'format' => 'Comma separated (e.g., "1,2" for Salary Slips and Timesheet)'
                ],
                'message' => 'Menu access retrieved successfully based on emp_masters.akses field'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get user menu access failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to get menu access',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================================================
    // NEW METHODS FOR EMPLOYEE FILTER SUPPORT - TIDAK MENGUBAH EXISTING CODE
    // ============================================================================

    /**
     * ✅ UPDATED: Get list of employees yang memiliki timesheet (from database)
     * GET /api/timesheet/employees
     */
    public function getTimesheetEmployees(Request $request)
    {
        try {
            $employees = [];
            
            foreach (self::$employeePageMapping as $empno => $page) {
                $employeeInfo = $this->getEmployeeInfo($empno);
                
                $employees[] = [
                    'empno' => $empno,
                    'name' => $employeeInfo['employee_name'],
                    'fullname' => $employeeInfo['employee_name'],
                    'page_number' => $page,
                    'exists_in_database' => $employeeInfo['exists_in_database'],
                    'timesheet_available' => $employeeInfo['timesheet_available'],
                    'data_source' => $employeeInfo['exists_in_database'] ? 'emp_masters_table' : 'fallback_empno'
                ];
            }

            // Sort by empno
            usort($employees, function($a, $b) {
                return strcmp($a['empno'], $b['empno']);
            });

            // Statistik untuk informasi
            $totalEmployees = count($employees);
            $databaseEmployees = count(array_filter($employees, function($emp) {
                return $emp['exists_in_database'];
            }));
            $fallbackEmployees = $totalEmployees - $databaseEmployees;

            return response()->json([
                'success' => true,
                'data' => $employees,
                'total' => $totalEmployees,
                'statistics' => [
                    'total_timesheet_employees' => $totalEmployees,
                    'found_in_database' => $databaseEmployees,
                    'fallback_empno_only' => $fallbackEmployees,
                    'database_coverage' => round(($databaseEmployees / $totalEmployees) * 100, 1) . '%'
                ],
                'message' => 'Employees with timesheet access retrieved successfully (dynamic from emp_masters table)',
                'note' => 'Employee names are fetched dynamically from emp_masters table. Fallback to empno if not found in database.'
            ]);

        } catch (\Exception $e) {
            Log::error('Get timesheet employees error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get timesheet employees: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get employees yang ada di database tapi tidak ada timesheet
     * GET /api/timesheet/employees/without-timesheet
     */
    public function getEmployeesWithoutTimesheet(Request $request)
    {
        try {
            // Get all employees from database
            $allEmployees = emp_master::select('empno', 'fullname')
                ->whereNotNull('empno')
                ->where('empno', '!=', '')
                ->get();
            
            $employeesWithoutTimesheet = [];
            $timesheetEmpnos = array_keys(self::$employeePageMapping);
            
            foreach ($allEmployees as $employee) {
                if (!in_array($employee->empno, $timesheetEmpnos)) {
                    $employeesWithoutTimesheet[] = [
                        'empno' => $employee->empno,
                        'fullname' => $employee->fullname,
                        'message' => "Timesheet not available for {$employee->fullname} (empno: {$employee->empno})"
                    ];
                }
            }
            
            // Sort by empno
            usort($employeesWithoutTimesheet, function($a, $b) {
                return strcmp($a['empno'], $b['empno']);
            });
            
            return response()->json([
                'success' => true,
                'data' => $employeesWithoutTimesheet,
                'total_without_timesheet' => count($employeesWithoutTimesheet),
                'total_in_database' => $allEmployees->count(),
                'total_with_timesheet' => count(self::$employeePageMapping),
                'coverage_percentage' => round((count(self::$employeePageMapping) / $allEmployees->count()) * 100, 1) . '%',
                'message' => 'Employees found in database but without timesheet access'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get employees without timesheet error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get employees without timesheet: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get timesheet berdasarkan filter empno dan/atau period
     * Support query parameters: empno, period
     * GET /api/timesheet/periods?empno=EMP001&period=202401
     */
    public function getAvailablePeriodsFiltered(Request $request)
    {
        try {
            $filterEmpno = $request->query('empno');
            $filterPeriod = $request->query('period');
            
            Log::info('Timesheet periods filtered request', [
                'filter_empno' => $filterEmpno,
                'filter_period' => $filterPeriod,
                'user_id' => Auth::id()
            ]);

            // Get current user empno if no filter provided
            $currentUser = Auth::user();
            $currentUserEmpno = $this->getUserEmployeeNumber($currentUser);

            // If no empno filter, use current user's empno
            if (!$filterEmpno) {
                $filterEmpno = $currentUserEmpno;
            }

            // Validate empno exists in mapping
            if ($filterEmpno && !isset(self::$employeePageMapping[$filterEmpno])) {
                return response()->json([
                    'success' => false,
                    'message' => "Employee {$filterEmpno} not found in timesheet mapping",
                    'data' => [],
                    'available_employees' => array_keys(self::$employeePageMapping)
                ], 404);
            }

            // Get available periods
            $availablePeriods = $this->scanAvailablePeriods();
            
            if ($filterPeriod) {
                $availablePeriods = array_filter($availablePeriods, function($periodData) use ($filterPeriod) {
                    return $periodData['period'] === $filterPeriod;
                });
            }

            $result = [];
            foreach ($availablePeriods as $periodData) {
                $period = $periodData['period'];
                $pdfPath = $periodData['pdf_path'];
                
                $timesheetEntry = [
                    'period' => $period,
                    'period_formatted' => self::formatPeriodDisplay($period),
                    'filename' => basename($pdfPath),
                    'file_size_mb' => round(filesize($pdfPath) / 1024 / 1024, 2),
                    'page_count' => $this->getPdfPageCount($pdfPath),
                    'has_pdf' => true,
                    'created_at' => date('Y-m-d\TH:i:s\Z', filemtime($pdfPath)),
                ];

                // Add employee specific info if empno filter applied
                if ($filterEmpno) {
                    $employeeName = $this->getEmployeeName($filterEmpno);
                    $pageNumber = self::$employeePageMapping[$filterEmpno];
                    
                    $timesheetEntry['employee_name'] = $employeeName;
                    $timesheetEntry['empno'] = $filterEmpno;
                    $timesheetEntry['period_mapping'] = [
                        'year' => substr($period, 0, 4),
                        'month' => substr($period, 4, 2),
                        'page_number' => $pageNumber
                    ];
                    $timesheetEntry['access_info'] = [
                        'pdf_url' => url("/api/timesheet/extract-page/{$period}") . ($filterEmpno !== $currentUserEmpno ? "?empno={$filterEmpno}" : ''),
                        'page_number' => $pageNumber,
                        'employee_name' => $employeeName
                    ];
                } else {
                    // Generic info without employee specific data
                    $timesheetEntry['employee_name'] = 'Multiple Employees';
                    $timesheetEntry['empno'] = '';
                    $timesheetEntry['access_info'] = [
                        'pdf_url' => url("/api/timesheet/extract-page/{$period}"),
                        'note' => 'Will show page based on logged in user'
                    ];
                }

                $result[] = $timesheetEntry;
            }

            // Sort by period descending
            usort($result, function($a, $b) {
                return strcmp($b['period'], $a['period']);
            });

            return response()->json([
                'success' => true,
                'data' => $result,
                'total' => count($result),
                'filters_applied' => [
                    'empno' => $filterEmpno,
                    'period' => $filterPeriod
                ],
                'mapping_info' => [
                    'total_employees' => count(self::$employeePageMapping),
                    'current_user_empno' => $currentUserEmpno,
                    'filter_empno' => $filterEmpno,
                    'is_filtered_by_employee' => !is_null($filterEmpno)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get timesheet periods filtered error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get timesheet periods: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Extract employee page dengan support empno parameter
     * GET /api/timesheet/extract-page/{period}?empno=EMP001
     */
    public function extractEmployeePageWithFilter(Request $request, $period)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'message' => 'Please login first'
                ], 401);
            }
            
            // Check if empno parameter provided
            $requestedEmpno = $request->query('empno');
            $currentUserEmpno = $this->getUserEmployeeNumber($user);
            
            // Use requested empno if provided, otherwise use current user's empno
            $targetEmpno = $requestedEmpno ?: $currentUserEmpno;
            
            if (!$targetEmpno) {
                return response()->json([
                    'error' => 'Employee number not found',
                    'message' => 'No employee number provided or found for user',
                    'debug' => [
                        'user_id' => $user->id,
                        'requested_empno' => $requestedEmpno,
                        'current_user_empno' => $currentUserEmpno
                    ]
                ], 400);
            }

            // Check if employee has a mapped page
            if (!isset(self::$employeePageMapping[$targetEmpno])) {
                return response()->json([
                    'error' => 'Employee page mapping not found',
                    'message' => "No timesheet page found for employee: {$targetEmpno}",
                    'available_employees' => array_keys(self::$employeePageMapping)
                ], 404);
            }

            $pageNumber = self::$employeePageMapping[$targetEmpno];
            
            // Find PDF file
            $sourcePdfPath = $this->findPdfFile($period);
            if (!$sourcePdfPath) {
                return response()->json([
                    'error' => 'Timesheet PDF not found',
                    'message' => "PDF file not found for period: {$period}",
                    'checked_paths' => $this->getPossiblePdfPaths($period)
                ], 404);
            }
            
            $fileSize = filesize($sourcePdfPath);
            $totalPages = $this->getPdfPageCount($sourcePdfPath);
            $employeeName = $this->getEmployeeName($targetEmpno);
            
            Log::info('Timesheet accessed with filter', [
                'target_empno' => $targetEmpno,
                'requested_empno' => $requestedEmpno,
                'current_user_empno' => $currentUserEmpno,
                'employee_name' => $employeeName,
                'period' => $period,
                'page' => $pageNumber,
                'user_id' => $user->id
            ]);
            
            // Return full PDF dengan employee page info di headers
            return response(file_get_contents($sourcePdfPath))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="timesheet_' . $targetEmpno . '_' . $period . '.pdf"')
                ->header('X-Employee-No', $targetEmpno)
                ->header('X-Target-Page', $pageNumber)
                ->header('X-Employee-Name', $employeeName)
                ->header('X-Total-Pages', $totalPages)
                ->header('X-File-Size-MB', round($fileSize / 1024 / 1024, 2))
                ->header('X-Period', $period)
                ->header('X-Period-Formatted', self::formatPeriodDisplay($period))
                ->header('X-Extraction-Method', $requestedEmpno ? 'filtered-empno-access' : 'direct-empno-access')
                ->header('X-Page-Navigation', 'auto-navigate-to-employee')
                ->header('X-Filter-Applied', $requestedEmpno ? 'true' : 'false')
                ->header('X-Instructions', 'PDF will auto-navigate to page ' . $pageNumber . ' for ' . $employeeName)
                ->header('X-Security', 'employee-isolated-access');
                
        } catch (\Exception $e) {
            Log::error('Timesheet extraction with filter error: ' . $e->getMessage(), [
                'period' => $period,
                'requested_empno' => $request->query('empno'),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to get timesheet',
                'message' => $e->getMessage(),
                'period' => $period
            ], 500);
        }
    }

    /**
     * Helper method untuk scan available periods
     */
    private function scanAvailablePeriods()
    {
        $periods = [];
        
        $possiblePaths = [
            public_path('assets/timesheet'),
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
            return [];
        }
        
        $files = array_values(array_diff(scandir($timesheetPath), ['.', '..']));
        $pdfFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
        });
        
        foreach ($pdfFiles as $file) {
            if (preg_match('/^(\d{6})\.pdf$/', $file, $matches)) {
                $period = $matches[1];
                $filePath = $timesheetPath . '/' . $file;
                
                $periods[] = [
                    'period' => $period,
                    'filename' => $file,
                    'pdf_path' => $filePath
                ];
            }
        }
        
        return $periods;
    }

    /**
     * ✅ NEW: Extract single page from PDF using FPDI
     * Returns PDF content with only the specified page
     */
    private function extractSinglePageFromPdf($sourcePdfPath, $pageNumber)
    {
        try {
            Log::info('PDF extraction started', [
                'source_pdf' => $sourcePdfPath,
                'page_number' => $pageNumber,
                'source_file_exists' => file_exists($sourcePdfPath),
                'source_file_size' => file_exists($sourcePdfPath) ? filesize($sourcePdfPath) : 0
            ]);

            // Create new FPDI instance
            $pdf = new Fpdi();
            
            // Set source file
            $pageCount = $pdf->setSourceFile($sourcePdfPath);
            
            Log::info('PDF source loaded', [
                'total_pages' => $pageCount,
                'requested_page' => $pageNumber
            ]);
            
            // Check if requested page exists
            if ($pageNumber > $pageCount || $pageNumber < 1) {
                Log::error("Page {$pageNumber} not found in PDF. Total pages: {$pageCount}");
                return false;
            }
            
            // Import the specified page
            $templateId = $pdf->importPage($pageNumber);
            
            // Get size of the imported page
            $size = $pdf->getTemplateSize($templateId);
            
            // Add a page with the same size as the original
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            
            // Use the imported page and scale to fit
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
            
            // Return PDF content as string
            $extractedContent = $pdf->Output('S'); // 'S' returns string instead of sending to browser
            
            Log::info('PDF extraction completed', [
                'extracted_content_size' => strlen($extractedContent),
                'original_page_size' => $size,
                'extraction_successful' => !empty($extractedContent)
            ]);
            
            return $extractedContent;
            
        } catch (Exception $e) {
            Log::error('PDF page extraction failed: ' . $e->getMessage(), [
                'source_pdf' => $sourcePdfPath,
                'page_number' => $pageNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
}