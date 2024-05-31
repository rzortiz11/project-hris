<?php

namespace App\Filament\Resources\LeaveSelfServiceResource\Pages;

use App\Filament\Resources\LeaveResource;
use App\Filament\Resources\LeaveSelfServiceResource;
use App\Livewire\CreateLeaveForm;
use App\Livewire\EmployeeLeaveHistoryTable;
use Filament\Actions;
use Filament\Actions\Action;
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
                    Grid::make()
                    ->schema([
                        Section::make("EMPLOYEE LEAVE APPROVER'S")
                            ->description('LEAVE APPROVERS')
                            ->icon('heroicon-o-shield-check')
                            ->columnSpan(6)
                            ->schema([
                                Livewire::make(CreateLeaveForm::class)->lazy()
                            ]),
                        Section::make('EMPLOYE LEAVE DETAILS')
                            ->description('LEAVE ALLOCATIONS')
                            ->icon('heroicon-o-document-duplicate')
                            ->columnSpan(3)
                            ->schema([
                                // Add your form components here
                            ]),
                        Section::make('WIDGET OR STATISTICS ALLOCATION DETAILS')
                            ->description('LEAVE ALLOCATIONS')
                            ->icon('heroicon-o-chart-pie')
                            ->columnSpan(3)
                            ->schema([
                                // Add your form components here
                            ]),
                    ])
                    ->columns(12),
                    Section::make('EMPLOYEE LEAVE HISTORY')
                    ->icon('heroicon-s-document-duplicate')
                    ->schema([
                        Livewire::make(EmployeeLeaveHistoryTable::class)->lazy()
                    ])
                ]),
            ]);
    }

}
