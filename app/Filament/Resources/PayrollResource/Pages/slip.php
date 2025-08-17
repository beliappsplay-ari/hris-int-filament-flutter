<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\Payroll;
use Filament\Resources\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;

class Slip extends Page
{
    protected static string $resource = PayrollResource::class;
    protected static string $view = 'filament.resources.payroll-resource.pages.slip';

    public $record;
    public $payroll;

    public function mount($record): void
    {
        $this->record = $record;
        $this->payroll = Payroll::with(['employee', 'user'])->find($record);
        
        if (!$this->payroll) {
            abort(404, 'Payroll record not found');
        }
    }

    public function viewPdf()
    {
        $payroll = $this->payroll;
        $pdf = Pdf::loadView('payrolls.salary-slip', compact('payroll'));
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'salary-slip-' . $payroll->empno . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="salary-slip-' . $payroll->empno . '.pdf"'
        ]);
    }

    public function downloadPdf()
    {
        $payroll = $this->payroll;
        $pdf = Pdf::loadView('payrolls.salary-slip', compact('payroll'));
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'salary-slip-' . $payroll->empno . '.pdf');
    }

    public function getTitle(): string
    {
        return 'Salary Slip - ' . ($this->payroll->employee->fullname ?? $this->payroll->empno ?? 'Unknown');
    }
}