<?php
namespace App\Filament\Resources\EmpMasterResource\Pages;
use App\Filament\Resources\EmpMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\emp_master;

class ListEmpMasters extends ListRecords
{
    protected static string $resource = EmpMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

   protected function getTableQuery(): Builder
     {
      return emp_master::where('users_id', Auth::user()->id);
    }
}
