<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Livewire\EmployeeTimeLogs;
use App\Livewire\EmployeeTimeSheet;
use App\Livewire\OverTimeRequestTable;
use App\Livewire\ShiftChangeRequestTable;
use App\Livewire\TimeChangeRequestTable;
use App\Livewire\UnderTimeRequestTable;
use App\Models\Employee;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\Grid;
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
use Illuminate\Support\Str;


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
                Grid::make()
                ->schema([
                    Section::make("Hour's this week")
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('employee_id')->label('')
                        ->getStateUsing(function (Employee $record): string {
                            // Get the current week's timesheets for the employee
                            $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
                            $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

                            $timesheets = TimeSheet::where('employee_id', $record->employee_id)
                                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                                ->get();

                            return self::calculateHoursFromTimeSheets($timesheets);
                        })
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),
                    ])->columnSpan(3)
                    ->extraAttributes(['style' => 'background-color:#007BFF']),
                    Section::make("Hour's this month")
                    ->schema([
                        TextEntry::make('employee_id')->label('')
                        ->getStateUsing(function (Employee $record): string {
                            // Get the current month's timesheets for the employee
                            $startOfMonth = now()->startOfMonth();
                            $endOfMonth = now()->endOfMonth();
    
                            $timesheets = TimeSheet::where('employee_id', $record->employee_id)
                                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                ->get();

                            return  self::calculateHoursFromTimeSheets($timesheets);
                        })
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),
                    ])->columnSpan(3)
                    ->extraAttributes(['style' => 'background-color:#28A745']),
                    Section::make("Total Late this month")
                    ->schema([
                        TextEntry::make('employee_id')->label('')
                            ->getStateUsing(function (Employee $record): string {
                                // Get the current month's timesheets for the employee
                                $startOfMonth = now()->startOfMonth();
                                $endOfMonth = now()->endOfMonth();
        
                                $timesheets = TimeSheet::where('employee_id', $record->employee_id)
                                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                    ->get();
        
                                return self::calculateLateTimeFromTimeSheets($timesheets);
                            })
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),
                    ])->columnSpan(3)
                    ->extraAttributes(['style' => 'background-color:#FFC107']),
                    Section::make("Total Over Time this month")
                    ->schema([
                        TextEntry::make('employee_id')->label('')
                            ->getStateUsing(function (Employee $record): string {
                                // Get the current month's timesheets for the employee
                                $startOfMonth = now()->startOfMonth();
                                $endOfMonth = now()->endOfMonth();
        
                                $timesheets = TimeSheet::where('employee_id', $record->employee_id)
                                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                    ->get();
        
                                return self::calculateOverTimeFromTimeSheets($timesheets);
                            })
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),
                    ])->columnSpan(3)
                    ->extraAttributes(['style' => 'background-color:#DC3545']),
                ])->columns(12),


                // Section::make('Attendance Details')
                // ->description('Employee Time Sheet')
                // ->icon('heroicon-s-clock')
                Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Time Sheet')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Livewire::make(EmployeeTimeSheet::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]),
                    Tab::make('Time Logs')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->schema([
                        Livewire::make(EmployeeTimeLogs::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]),
                    Tab::make('Time Change')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->schema([
                        Livewire::make(TimeChangeRequestTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]), 
                    Tab::make('Over Time')
                    ->icon('heroicon-o-window')
                    ->schema([
                        Livewire::make(OverTimeRequestTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]),      
                    Tab::make('Under Time')
                    ->icon('heroicon-m-arrow-uturn-down')
                    ->schema([
                        Livewire::make(UnderTimeRequestTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]),  
                    Tab::make('Shift Change')
                    ->icon('heroicon-o-rectangle-group')
                    ->schema([
                        Livewire::make(ShiftChangeRequestTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ]),                         
                ])
                ->persistTabInQueryString()
                ->contained(false)
                ->columnSpanFull(),
            ]);
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }

    // Define a function to calculate hours from time sheets
    public function calculateHoursFromTimeSheets($timesheets): string {
        $totalMinutes = 0;

        foreach ($timesheets as $timesheet) {
            // Extract and parse timesheet data
            $timeIn = $timesheet->time_in;
            $timeOut = $timesheet->time_out;
            $shiftSchedule = explode(' - ', $timesheet->shift_schedule);
            $shiftStart = Carbon::parse($shiftSchedule[0]);
            $shiftEnd = Carbon::parse($shiftSchedule[1]);

            // Skip entries where either time_in or time_out is not available or with 00:00
            if ($timeIn == '00:00:00' || $timeOut == '00:00:00') {
                continue;
            }

            $timeIn = Carbon::parse($timeIn);
            $timeOut = Carbon::parse($timeOut);

            // Ensure time_in and time_out fall within the shift schedule
            $actualStart = $timeIn->copy()->max($shiftStart);
            $actualEnd = $timeOut->copy()->min($shiftEnd);

            // Calculate minutes worked excluding the break
            $breakStart = Carbon::parse('12:00:00');
            $breakEnd = Carbon::parse('13:00:00');

            if ($actualStart < $breakStart && $actualEnd > $breakEnd) {
                // Work period spans the break period
                $minutesWorked = $actualStart->diffInMinutes($breakStart) + $breakEnd->diffInMinutes($actualEnd);
            } elseif ($actualStart < $breakStart && $actualEnd > $breakStart) {
                // Work period starts before break and ends during break
                $minutesWorked = $actualStart->diffInMinutes($breakStart);
            } elseif ($actualStart < $breakEnd && $actualEnd > $breakEnd) {
                // Work period starts during break and ends after break
                $minutesWorked = $breakEnd->diffInMinutes($actualEnd);
            } else {
                // Work period does not intersect with break
                $minutesWorked = $actualStart->diffInMinutes($actualEnd);
            }

            $totalMinutes += $minutesWorked;
        }

        // Convert total minutes to hours and minutes
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        // Format the result as HH:MM
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        // Return the total hours and minutes worked this week in HH:MM format
        return "{$formattedHours}:{$formattedMinutes}";
    }

    function calculateLateTimeFromTimeSheets($timesheets): string {
        $totalLateMinutes = 0;
    
        foreach ($timesheets as $timesheet) {
            $lateTime = $timesheet->late_time;
    
            if ($lateTime == '00:00:00') {
                continue;
            }
    
            $lateTime = Carbon::parse($lateTime);
            $totalLateMinutes += $lateTime->hour * 60 + $lateTime->minute;
        }
    
        // Convert total late minutes to hours and minutes
        $hours = floor($totalLateMinutes / 60);
        $minutes = $totalLateMinutes % 60;
    
        // Format the result as HH:MM
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    
        // Return the total late time in HH:MM format
        return "{$formattedHours}:{$formattedMinutes}";
    }

    function calculateOverTimeFromTimeSheets($timesheets): string {
        $totalLateMinutes = 0;
    
        foreach ($timesheets as $timesheet) {
            $lateTime = $timesheet->over_time;
    
            if ($lateTime == '00:00:00') {
                continue;
            }
    
            $lateTime = Carbon::parse($lateTime);
            $totalLateMinutes += $lateTime->hour * 60 + $lateTime->minute;
        }
    
        // Convert total late minutes to hours and minutes
        $hours = floor($totalLateMinutes / 60);
        $minutes = $totalLateMinutes % 60;
    
        // Format the result as HH:MM
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    
        // Return the total late time in HH:MM format
        return "{$formattedHours}:{$formattedMinutes}";
    }
}
