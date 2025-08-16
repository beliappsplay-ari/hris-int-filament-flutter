<?php

namespace App\Filament\Resources\EmpMasterResource\Pages;

use App\Filament\Resources\EmpMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpMaster extends EditRecord
{
    protected static string $resource = EmpMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
