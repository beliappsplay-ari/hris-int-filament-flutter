<?php

namespace App\Helpers;

class PdfHelper
{
    public static function viewPdfSlip($empno, $period, $isApi = false)
    {
        $months = [
            '01' => 'JAN', '02' => 'FEB', '03' => 'MAR', '04' => 'APR',
            '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AUG', 
            '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'
        ];
        
        $year = substr($period, 0, 4);
        $month = substr($period, 4, 2);
        $monthName = $months[$month] ?? 'JAN';
        
        $filename = $empno . '-' . $monthName . $year . '.pdf';
        $filePath = public_path('assets/slip/' . $filename);
        
        if (!file_exists($filePath)) {
            if ($isApi) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF file not found',
                    'data' => [
                        'empno' => $empno,
                        'period' => $period,
                        'expected_filename' => $filename,
                        'suggestion' => 'Please contact HR department'
                    ]
                ], 404);
            }
            
            return response()->json([
                'error' => 'PDF file not found',
                'expected_filename' => $filename,
                'expected_path' => $filePath,
                'directory_exists' => is_dir(public_path('assets/slip')),
                'files_in_directory' => is_dir(public_path('assets/slip')) ? 
                    array_slice(scandir(public_path('assets/slip')), 2) : 'Directory not found'
            ], 404);
        }
        
        // Untuk API, tambahkan metadata
        if ($isApi) {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'X-File-Name' => $filename,
                'X-File-Size' => filesize($filePath),
                'X-Employee-No' => $empno,
                'X-Period' => $period
            ]);
        }
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public static function formatPeriodForDisplay($period)
    {
        $months = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', 
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        $year = substr($period, 0, 4);
        $month = substr($period, 4, 2);
        $monthName = $months[$month] ?? 'Unknown';
        
        return "$monthName $year";
    }

    public static function checkPdfExists($empno, $period)
    {
        $months = [
            '01' => 'JAN', '02' => 'FEB', '03' => 'MAR', '04' => 'APR',
            '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AUG', 
            '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'
        ];
        
        $year = substr($period, 0, 4);
        $month = substr($period, 4, 2);
        $monthName = $months[$month] ?? 'JAN';
        $filename = $empno . '-' . $monthName . $year . '.pdf';
        $filePath = public_path('assets/slip/' . $filename);
        
        return file_exists($filePath);
    }
}