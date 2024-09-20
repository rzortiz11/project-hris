<?php

namespace App\Filament\Resources\MyPayslipResource\Pages;

use App\Filament\Resources\MyPayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;


class ViewMyPayslip extends ViewRecord
{
    protected static string $resource = MyPayslipResource::class;

    public function mount(int | string $record): void
    {
      
        if($record == 'payslip'){
            $employee = auth()->user()->employee;
            $record = $employee->employee_id;
        }

        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
        $this->fillForm();
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Livewire::make(MyPayslipViewTable::class)->key(self::generateUuid())
                ])
            ]);
    }
}
