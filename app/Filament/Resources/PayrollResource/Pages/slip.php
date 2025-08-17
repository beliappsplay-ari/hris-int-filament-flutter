<?php
namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Resources\Pages\Page;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class slip extends Page
{
    protected static string $resource = PayrollResource::class;
    protected static string $view = 'filament.resources.payroll-resource.pages.slip';

    public $record;
    public $payroll;

    public function mount($record)
    {
        $this->record = $record;
        // Load payroll dengan employee data
        $this->payroll = Payroll::with(['employee', 'user'])->find($record);
        
        // Debug: pastikan data loaded
        if (!$this->payroll) {
            abort(404, 'Payroll not found');
        }
    }

    // Method untuk download PDF
    public function downloadPdf()
    {
        $pdf = Pdf::loadView('payrolls.salary-slip', [
            'payroll' => $this->payroll
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'salary-slip-' . $this->payroll->empno . '-' . $this->payroll->period . '.pdf');
    }

    // Method untuk view PDF di browser
    public function viewPdf()
    {
        $pdf = Pdf::loadView('payrolls.salary-slip', [
            'payroll' => $this->payroll
        ]);
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="salary-slip-' . $this->payroll->empno . '.pdf"'
        ]);
    }
}