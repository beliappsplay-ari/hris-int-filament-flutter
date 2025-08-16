<?php
namespace App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\payroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

  //  public function getHeader(): ?View
    //{
      //  $data = Actions\CreateAction::make();
        //return view('filament.custom.upload-file',compact('data'));
    //}

    //public $file='';

    /*public function save(){
        Payroll::create([
            'empno' => '22971',
            'period' => '202501',
            'basicsalary' => '5000000',
            'overtime' => '0',
            'taxamonth' => '0',
            'total' => '5000000',
        ]);
    }*/

   // protected function getTableQuery(): Builder
     //{
      //return payroll::where('empno', 'empno');
    //}


    protected function getTableQuery(): Builder
    {
     return payroll::where('users_id', Auth::user()->id);
   }

}
