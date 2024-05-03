<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Livewire\EmployeeTimeSheet;
use App\Models\TimeSheet;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;

class ViewEmployeeTimeSheet extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getActions(): array
    {
        $actions = [];

        $actions[] = Action::make('return')
            ->color('info')
            ->label('Return')
            ->action(function () {
                 //artisan route:list to view the filament route list
                redirect()->route('filament.admin.resources.attendances.index');
            });

            return $actions;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TimeSheet::query()->where('employee_id', 19))
            ->columns([
     
            ])
            ->defaultSort('id', 'desc');
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('Attendance Details')
                ->description('Employee Time Sheet')
                ->icon('heroicon-s-clock')
                ->schema([
                    TextEntry::make('employee_reference')->label('Employee Reference')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('user.name')->label('Employee Name')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('position.job_category')->label('Category')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('position.job_position')->label('Position')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large),
                    TextEntry::make('position.reporting_designation')->label('Department')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large),
                ])->columns(5),

                // Section::make('Attendance Details')
                // ->description('Employee Time Sheet')
                // ->icon('heroicon-s-clock')
                Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Time Sheet')
                    ->schema([
                        Livewire::make(EmployeeTimeSheet::class)->data(['record' => $this->record])
                    ]),
                    Tab::make('Time Logs')
                    ->schema([
                        // ...
                    ]),                    
                ])
                ->contained(false)
                ->columnSpanFull(),
            ]);
    }

}
