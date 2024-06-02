<?php

namespace App\Filament\Resources\LeaveSelfServiceResource\Pages;

use App\Filament\Resources\LeaveResource;
use App\Filament\Resources\LeaveSelfServiceResource;
use App\Livewire\CreateLeaveForm;
use App\Livewire\EmployeeLeaveHistoryTable;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;


class EmployeeSelfServiceLeaveView extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

    public function mount(int | string $record): void
    {
        if($record == 'leave'){
            $employee = auth()->user()->employee;
            $record = $employee->employee_id;
        }

        $this->record = $this->resolveRecord($record);
        static::authorizeResourceAccess();
        $this->fillForm();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Tabs::make()
                    ->tabs([
                       Tab::make('Leave History')
                            ->schema([
                                Section::make('EMPLOYEE LEAVE HISTORY')
                                ->icon('heroicon-s-document-duplicate')
                                ->schema([
                                    Livewire::make(EmployeeLeaveHistoryTable::class)->lazy()
                                ])
                            ])->columns(2),
                       Tab::make('Leave Form')
                            ->schema([
                                Livewire::make(CreateLeaveForm::class)->lazy()    
                            ]),     
                    ])
                    ->contained(false)
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
                ]),
            ]);
    }

}
