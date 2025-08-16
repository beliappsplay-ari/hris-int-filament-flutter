<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Resources\Pages\Page;
use App\Models\Payroll;

class slip extends Page
{
    protected static string $resource = PayrollResource::class;

    public $record;
    public $payroll;

    public function mount($record)
    {
        $this->record = $record;
        $this->payroll = Payroll::find($record);
    }

    protected static string $view = 'filament.resources.payroll-resource.pages.slip';
}
