<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Livewire\EmployeeTimeLogs;
use App\Livewire\EmployeeTimeSheet;
use App\Models\Employee;
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

    public $isTimeSheetView = false;

    public function mount(int | string $record): void
    {
        if($record == 'timesheet'){
            $employee = auth()->user()->employee;
            $record = $employee->employee_id;
            $this->isTimeSheetView = true;
        }
        
        $this->record = $this->resolveRecord($record);
        
        // $this->authorizeAccess();
    
        // if (! $this->hasInfolist()) {
        //     $this->fillForm();
        // }

        static::authorizeResourceAccess();
        $this->fillForm();
    }

    protected $listeners = [
        'reviewSectionRefresh' => '$refresh',
    ];

    protected function getActions(): array
    {
        $actions = [];

        $actions[] = Action::make('return')
            ->color('info')
            ->label('Return')
            ->action(function () {
                 //artisan route:list to view the filament route list
                redirect()->route('filament.admin.resources.attendances.index');
            })
            ->hidden($this->isTimeSheetView);

            return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Attendance Details')
                ->description('Employee Time Sheet')
                ->icon('heroicon-s-clock')
                ->schema([
                    TextEntry::make('employee_reference')->label('Employee Number'),
                    TextEntry::make('user.name')->label('Employee Name'),
                    TextEntry::make('position.job_category')->label('Category'),
                    TextEntry::make('position.job_position')->label('Position'),
                    TextEntry::make('position.reporting_designation')->label('Department'),
                    // ->weight(FontWeight::Bold)
                    // ->size(TextEntry\TextEntrySize::Large),
                ])->columns(5),

                // Section::make('Attendance Details')
                // ->description('Employee Time Sheet')
                // ->icon('heroicon-s-clock')
                Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Time Sheet')
                    ->schema([
                        Livewire::make(EmployeeTimeSheet::class)->data(['record' => $this->record])->lazy()
                    ]),
                    Tab::make('Time Logs')
                    ->schema([
                        Livewire::make(EmployeeTimeLogs::class)->data(['record' => $this->record])->lazy()
                    ]),                    
                ])
                ->persistTabInQueryString()
                ->contained(false)
                ->columnSpanFull(),
            ]);
    }

}
