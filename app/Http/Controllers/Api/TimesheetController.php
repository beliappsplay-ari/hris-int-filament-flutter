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
            
            // Check if employee has a mapped page
            if (!isset(self::$employeePageMapping[$empno])) {
                return response()->json([
                    'error' => 'Employee page mapping not found',
                    'message' => "No timesheet page found for employee: {$empno}",
                    'available_employees' => array_keys(self::$employeePageMapping),
                    'debug' => [
                        'requested_empno' => $empno,
                        'available_mappings' => self::$employeePageMapping
                    ]
                ], 404);
            }
            
            $pageNumber = self::$employeePageMapping[$empno];
            
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
                'employee_name' => $this->getEmployeeName($empno),
                'period' => $period,
                'page' => $pageNumber,
                'file_size' => $fileSize,
                'total_pages' => $totalPages,
                'user_id' => $user->id
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
                ->header('X-Employee-Name', $this->getEmployeeName($empno))
                ->header('X-Total-Pages', 1) // ✅ Now only 1 page
                ->header('X-Original-Total-Pages', $totalPages)
                ->header('X-Extracted-Page', $pageNumber)
                ->header('X-File-Size-MB', round(strlen($extractedPdfContent) / 1024 / 1024, 2))
                ->header('X-Period', $period)
                ->header('X-Period-Formatted', self::formatPeriodDisplay($period))
                ->header('X-Extraction-Method', 'single-page-extract')
                ->header('X-Page-Navigation', 'page-' . $pageNumber . '-only')
                ->header('X-Instructions', 'PDF contains only page ' . $pageNumber . ' for ' . $this->getEmployeeName($empno))
                ->header('X-Security', 'employee-isolated-single-page');
                
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
     * Get employee name from mapping
     */
    private function getEmployeeName($empno)
    {
        $employeeNames = [
            '10132' => 'YOVITA E. WAHYUNINGRUM',
            '22682' => 'ALFA YUNITA SARASWATI',
            '22691' => 'ARIS WAHYU HANANTO',
            '22783' => 'SURADIMAN',
            '22785' => 'IKA FARADINA',
            '22817' => 'HENNI YULIASTARI',
            '22845' => 'INDAH DARMAWATI',
            '22905' => 'NILA FIRSTYA',
            '22928' => 'FADILA YASMIN',
            '22938' => 'IIN NURHAYATI',
            '22971' => 'ARI RAHMADI',
            '22976' => 'PUTRI KUSUMAWATI',
            '22988' => 'RIZKY RIENALDI',
            '22989' => 'ASMAH KHADAFIAH',
            '22997' => 'RUWIATIN SUGIARTO',
            '23084' => 'JONATHAN PAIDOTUA SIANTURI',
            '23089' => 'ABDUL ROZAK MUMTAZ',
            '23095' => 'REZKI ANGGRAENI',
            '23101' => 'DANIEL HERRY WIBOWO',
            '23105' => 'MUHAMMAD ALFIA RIZKI',
            '23106' => 'HERRY SABARUDIN',
            '23119' => 'MUHAMMAD ISMAIL HASIBUAN',
            '23123' => 'BAGUS PUJIYANTO',
            '23126' => 'SUKMA HASNAA AMBAROH',
            '23129' => 'SITI ASPIYAH',
            '23131' => 'GOLDY HERDANI RIVERO',
            '23132' => 'ALVINA KHOIRUNISA',
            '23133' => 'FITRIA ASANEGERI',
            '23135' => 'KARINA AYU ALDRIANA',
        ];
        
        return $employeeNames[$empno] ?? "Employee {$empno}";
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

    // ============================================================================
    // NEW METHODS FOR EMPLOYEE FILTER SUPPORT - TIDAK MENGUBAH EXISTING CODE
    // ============================================================================

    /**
     * Get list of employees yang memiliki timesheet
     * GET /api/timesheet/employees
     */
    public function getTimesheetEmployees(Request $request)
    {
        try {
            $employees = [];
            
            foreach (self::$employeePageMapping as $empno => $page) {
                $employeeName = $this->getEmployeeName($empno);
                $employees[] = [
                    'empno' => $empno,
                    'name' => $employeeName,
                    'fullname' => $employeeName,
                    'page_number' => $page
                ];
            }

            // Sort by empno
            usort($employees, function($a, $b) {
                return strcmp($a['empno'], $b['empno']);
            });

            return response()->json([
                'success' => true,
                'data' => $employees,
                'total' => count($employees),
                'message' => 'Employees with timesheet access retrieved successfully'
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