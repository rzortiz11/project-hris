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
use Illuminate\Support\Str;

class EmployeeSelfServiceLeaveView extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

    public function mount(int | string $record): void
    {
        if($record == 'leave'){
            $employee = auth()->user()->employee;
            if(isset($employee->employee_id)) {
                $record = $employee->employee_id;
            } 
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
                    Tabs::make()
                    ->tabs([
                       Tab::make('Leave History')
                        ->icon('heroicon-o-folder-open')
                        ->schema([
                            Section::make('EMPLOYEE LEAVE HISTORY')
                            ->icon('heroicon-s-document-duplicate')
                            ->schema([
                                Livewire::make(EmployeeLeaveHistoryTable::class)->key(self::generateUuid())
                            ])
                        ])->columns(2),
                    //    Tab::make('Request a Leave')
                    //     ->icon('heroicon-o-arrow-left-end-on-rectangle')
                    //     ->schema([
                    //         Livewire::make(CreateLeaveForm::class)->key(self::generateUuid())    
                    //     ]),     
                    ])
                    ->contained(false)
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
                ]),
            ]);
    }

}
