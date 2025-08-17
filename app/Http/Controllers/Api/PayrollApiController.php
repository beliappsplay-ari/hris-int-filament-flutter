<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollApiController extends Controller
{
    /**
     * Get all salary slips for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get employee data from user
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee data not found for this user'
                ], 404);
            }
            
            // Get payrolls for this employee
            $payrolls = Payroll::with(['employee', 'user'])
                ->where('empno', $employee->empno)
                ->orderBy('period', 'desc')
                ->get();
            
            // Transform data for Flutter
            $salarySlips = $payrolls->map(function ($payroll) {
                return [
                    'id' => $payroll->id,
                    'period' => $payroll->period,
                    'formatted_period' => $payroll->formatted_period ?? $payroll->period,
                    'empno' => $payroll->empno,
                    'employee' => [
                        'fullname' => $payroll->employee->fullname ?? 'N/A',
                        'empno' => $payroll->empno
                    ],
                    'employee_name' => $payroll->employee->fullname ?? 'N/A',
                    'basic_salary' => $payroll->basicsalary ?? 0,
                    'allowances' => ($payroll->transport ?? 0) + 
                                  ($payroll->meal ?? 0) + 
                                  ($payroll->overtime ?? 0) + 
                                  ($payroll->otherincome ?? 0) + 
                                  ($payroll->medical ?? 0),
                    'deductions' => ($payroll->taxamonth ?? 0) + 
                                  ($payroll->jkm ?? 0) + 
                                  ($payroll->jht ?? 0) + 
                                  ($payroll->bpjskaryawan ?? 0) + 
                                  ($payroll->bpjsperusahaan ?? 0) + 
                                  ($payroll->personaladvance ?? 0) + 
                                  ($payroll->koperasi ?? 0) + 
                                  ($payroll->loancar ?? 0),
                    'net_salary' => $payroll->thp ?? 0,
                    'total_income' => $payroll->total ?? 0,
                    'created_at' => $payroll->created_at,
                    'updated_at' => $payroll->updated_at,
                    
                    // Detail breakdown for view details
                    'details' => [
                        'income' => [
                            'basic_salary' => $payroll->basicsalary ?? 0,
                            'transport' => $payroll->transport ?? 0,
                            'meal' => $payroll->meal ?? 0,
                            'overtime' => $payroll->overtime ?? 0,
                            'other_income' => $payroll->otherincome ?? 0,
                            'medical' => $payroll->medical ?? 0,
                            'bpjs_company' => $payroll->bpjsperusahaan ?? 0,
                        ],
                        'deductions' => [
                            'tax' => $payroll->taxamonth ?? 0,
                            'jkm' => $payroll->jkm ?? 0,
                            'jht' => $payroll->jht ?? 0,
                            'bpjs_employee' => $payroll->bpjskaryawan ?? 0,
                            'bpjs_company' => $payroll->bpjsperusahaan ?? 0,
                            'personal_advance' => $payroll->personaladvance ?? 0,
                            'koperasi' => $payroll->koperasi ?? 0,
                            'loan_car' => $payroll->loancar ?? 0,
                        ]
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Salary slips retrieved successfully',
                'data' => $salarySlips
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching salary slips: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching salary slips: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get specific salary slip details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee data not found for this user'
                ], 404);
            }
            
            // Find payroll and ensure it belongs to this employee
            $payroll = Payroll::with(['employee', 'user'])
                ->where('id', $id)
                ->where('empno', $employee->empno)
                ->first();
            
            if (!$payroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary slip not found or access denied'
                ], 404);
            }
            
            // Transform single payroll data
            $salarySlip = [
                'id' => $payroll->id,
                'period' => $payroll->period,
                'formatted_period' => $payroll->formatted_period ?? $payroll->period,
                'empno' => $payroll->empno,
                'employee' => [
                    'fullname' => $payroll->employee->fullname ?? 'N/A',
                    'empno' => $payroll->empno
                ],
                'employee_name' => $payroll->employee->fullname ?? 'N/A',
                'basic_salary' => $payroll->basicsalary ?? 0,
                'allowances' => ($payroll->transport ?? 0) + 
                              ($payroll->meal ?? 0) + 
                              ($payroll->overtime ?? 0) + 
                              ($payroll->otherincome ?? 0) + 
                              ($payroll->medical ?? 0),
                'deductions' => ($payroll->taxamonth ?? 0) + 
                              ($payroll->jkm ?? 0) + 
                              ($payroll->jht ?? 0) + 
                              ($payroll->bpjskaryawan ?? 0) + 
                              ($payroll->bpjsperusahaan ?? 0) + 
                              ($payroll->personaladvance ?? 0) + 
                              ($payroll->koperasi ?? 0) + 
                              ($payroll->loancar ?? 0),
                'net_salary' => $payroll->thp ?? 0,
                'total_income' => $payroll->total ?? 0,
                'pdf_url' => route('api.salary-slips.pdf', $id),
                
                // Full details
                'details' => [
                    'income' => [
                        'basic_salary' => $payroll->basicsalary ?? 0,
                        'transport' => $payroll->transport ?? 0,
                        'meal' => $payroll->meal ?? 0,
                        'overtime' => $payroll->overtime ?? 0,
                        'other_income' => $payroll->otherincome ?? 0,
                        'medical' => $payroll->medical ?? 0,
                        'bpjs_company' => $payroll->bpjsperusahaan ?? 0,
                    ],
                    'deductions' => [
                        'tax' => $payroll->taxamonth ?? 0,
                        'jkm' => $payroll->jkm ?? 0,
                        'jht' => $payroll->jht ?? 0,
                        'bpjs_employee' => $payroll->bpjskaryawan ?? 0,
                        'bpjs_company' => $payroll->bpjsperusahaan ?? 0,
                        'personal_advance' => $payroll->personaladvance ?? 0,
                        'koperasi' => $payroll->koperasi ?? 0,
                        'loan_car' => $payroll->loancar ?? 0,
                    ]
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Salary slip retrieved successfully',
                'data' => $salarySlip
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching salary slip: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching salary slip: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download salary slip as PDF
     */
    public function downloadPdf(Request $request, $id)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee data not found for this user'
                ], 404);
            }
            
            // Find payroll and ensure it belongs to this employee
            $payroll = Payroll::with(['employee', 'user'])
                ->where('id', $id)
                ->where('empno', $employee->empno)
                ->first();
            
            if (!$payroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary slip not found or access denied'
                ], 404);
            }
            
            // Generate PDF using the same view as web routes
            $pdf = Pdf::loadView('payrolls.salary-slip', ['payroll' => $payroll]);
            
            $fileName = "salary_slip_{$payroll->empno}_{$payroll->period}.pdf";
            
            // Return PDF as stream download for Flutter
            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->stream();
                },
                $fileName,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * View PDF inline (for testing in browser)
     */
    public function viewPdf(Request $request, $id)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee data not found for this user'
                ], 404);
            }
            
            // Find payroll and ensure it belongs to this employee
            $payroll = Payroll::with(['employee', 'user'])
                ->where('id', $id)
                ->where('empno', $employee->empno)
                ->first();
            
            if (!$payroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary slip not found or access denied'
                ], 404);
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('payrolls.salary-slip', ['payroll' => $payroll]);
            
            $fileName = "salary_slip_{$payroll->empno}_{$payroll->period}.pdf";
            
            // Return PDF for inline viewing
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
            
        } catch (\Exception $e) {
            \Log::error('Error viewing PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error viewing PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}