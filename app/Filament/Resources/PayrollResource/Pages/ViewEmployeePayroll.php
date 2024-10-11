<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Carbon\Carbon;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ViewEmployeePayroll extends ViewRecord
{
    protected static string $resource = PayrollResource::class;

    public $isPayrollView = false;

    public function mount(int | string $record): void
    {
        if($record == 'employee-payrolls'){
            $employee = auth()->user()->employee;
            $record = $employee->employee_id;
            $this->isPayrollView = true;
        }
        
        $this->record = $this->resolveRecord($record);
     
        static::authorizeResourceAccess();
        $this->fillForm();
    }

    protected function getActions(): array
    {
        $actions = [];
        $actions[] = Action::make('return')
        ->color('info')
        ->label('Return')
        ->action(function () {
            redirect()->route('filament.admin.resources.payrolls.index');
        })
        ->hidden($this->isPayrollView);

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Group::make()
            ->schema([
                TextEntry::make('type'),
                TextEntry::make('start_date')->date(),
                TextEntry::make('end_date')->date(),
                TextEntry::make('cut_off_date')->date(),
                TextEntry::make('created_by'),
                TextEntry::make('created_at')->date(),
            ])->columns(3),
        ]);
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }
}
