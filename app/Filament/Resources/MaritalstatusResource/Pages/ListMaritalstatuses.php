<?php

namespace App\Filament\Resources\MaritalstatusResource\Pages;

use App\Filament\Resources\MaritalstatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaritalstatuses extends ListRecords
{
    protected static string $resource = MaritalstatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
