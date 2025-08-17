<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function salarySlip($id)
    {
        // Manual find dengan ID
        $payroll = Payroll::with(['employee', 'user'])->findOrFail($id);
        
        // Generate PDF
        $pdf = Pdf::loadView('payrolls.salary-slip', ['payroll' => $payroll]);
        
        // Return PDF stream untuk preview (buka di browser)
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="salary-slip-' . $payroll->empno . '.pdf"');
    }
    
    public function downloadSalarySlip($id)
    {
        // Manual find dengan ID
        $payroll = Payroll::with(['employee', 'user'])->findOrFail($id);
        
        // Generate PDF
        $pdf = Pdf::loadView('payrolls.salary-slip', ['payroll' => $payroll]);
        
        // Return PDF download
        return $pdf->download('salary-slip-' . $payroll->empno . '.pdf');
    }
}