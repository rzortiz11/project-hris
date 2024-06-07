<?php

namespace App\Filament\Pages;

use App\Livewire\AnalogClock;
use App\Livewire\EmployeeOnLeaveTable;
use App\Livewire\EmployeeUpcomingLeaveTable;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Forms\Components;
use Filament\Resources\Forms\ResourceForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class Dashboard extends Page implements HasForms
{
    
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';
   
    use InteractsWithForms;

    use HasFiltersForm;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->form([
                TextInput::make('test')

            ])
            ->action(function() {
                dd(1);
            })
        ];
    }
    
    public function form(Form $form): Form
    {
        $user_id = auth()->id();
        $employee = Employee::where('user_id', $user_id)->first();
        $current_date = now()->toDateString();
        $current_time = now()->format('H:i');
        $timesheet = '';

        $timesheet = isset($employee) ? $employee->employee_timesheets()->where('date', $current_date)->first() : "";
        
        return $form
        ->schema([
            Grid::make()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->columnSpan(3)
                ->schema([
                    Section::make('')
                    ->schema([
                        Livewire::make(AnalogClock::class)
                    ])
                    ->extraAttributes([
                        'class' => 'flex justify-center items-center text-center',
                        'style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);margin-top: 75px;'
                    ])
                    ->columns(1),
                    static::timeInSection($employee, $current_date, $current_time, $timesheet),
                    static::timeOutSection($employee, $current_date, $current_time, $timesheet),
                ])
                ->extraAttributes(['class' => 'bg-gray-600']),    
                Grid::make([
                    'default' => 1
                ])
                ->columnSpan(9)
                ->schema([
                    Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Company News & Announcement')
                        ->icon('heroicon-o-megaphone')
                        ->schema([
                            Livewire::make(EmployeeOnLeaveTable::class)
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make('On Leave Today')
                        ->icon('heroicon-o-users')
                        ->schema([
                            // Livewire::make(EmployeeOnLeaveTable::class)
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make('Upcoming Leaves This Week')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            // Livewire::make(EmployeeUpcomingLeaveTable::class)
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make('Events & Holiday Calendar')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            // Livewire::make(EmployeeUpcomingLeaveTable::class)
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
                ]),
            ])
            ->columns(12),
        ]);
    }

    
    public static function timeInSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make()
        ->description()
        ->schema([
            Section::make('Time in')
            ->icon('heroicon-o-clock')
            ->id('Date and Time in')
            ->schema([
                Placeholder::make('time')->label('Time')
                ->content(function () use($timesheet) {

                    if(isset($timesheet->time_in)){
                        if($timesheet->time_in != '00:00:00'){
                            return date('h:i A', strtotime($timesheet->time_in));
                        }
                    }

                    return '-- : --';
                })
            ])
            ->footerActions([
                FormAction::make('Time in')
                    ->form([
                        Placeholder::make('Do you want to time in?'),
                        Select::make('location')
                        ->options([
                            'OFFICE' => 'Office',
                            'ONFIELD' => 'Onfield',
                            'WFH' => 'Work From Home',
                        ])->required(),   
                    ])
                    ->disabled(function () use($timesheet) {
                        
                        if(isset($timesheet->time_in)){
                            if($timesheet->time_in != '00:00:00'){
                                return true;
                            }
                        }

                        return false;
                    })
                    ->action(function (array $data) use ($employee, $current_date, $current_time, $timesheet) {

                        $location = $data['location'];

                        if($employee){

                            $result = $employee->employee_timelogs()->create([
                                    'date' => $current_date,
                                    'day' => now()->format('l'),
                                    'type' => 'TIMEIN',
                                    'time' => $current_time,
                                    'location' => $location
                            ]);

                            if($result) {
                                
                                if ($timesheet) {
                                    
                                    $timesheet->time_in = $current_time;
                                    $timesheet->in_location = $location;
                                    $timesheet->save();
                                } else {

                                    // if no timesheet create for this attendance
                                    $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
                                    $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
                                    $schedule = $time_in . ' - ' . $time_out;

                                    $employee->employee_timesheets()->create([
                                        'date' => $current_date,
                                        'shift_schedule' => $schedule,
                                        'time_in' => $current_time,
                                        'in_location' => $location,
                                    ]);
                                }
                                
                                Notification::make()
                                ->success()
                                ->title('Attendace')
                                ->body('Time in '.date('h:i A', strtotime($current_time)).' successfully.')
                                ->send();
                            }
                        }
                    })
                    ->icon('heroicon-o-clock')
                    ->tooltip('Do you want to Time in!'),
            ])  
            ->footerActionsAlignment(Alignment::Center)    
        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']);
    } 

    public static function timeOutSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make()
        ->schema([
            Section::make('Time out')
            ->icon('heroicon-s-clock')
            ->id('Date and Time out')
            ->schema([
                Placeholder::make('time')->label('Time')
                ->content(function () use($timesheet) {

                    if(isset($timesheet->time_out)){
                        if($timesheet->time_out != '00:00:00'){
                            return date('h:i A', strtotime($timesheet->time_out));
                        }
                    }

                    return '-- : --';
                })
            ])
            ->footerActions([
                FormAction::make('Time out')
                ->form([
                    Placeholder::make('are you sure you want to time out?'),
                    Select::make('location')
                    ->options([
                        'OFFICE' => 'Office',
                        'ONFIELD' => 'Onfield',
                        'WFH' => 'Work From Home',
                    ])->required(),   
                ])
                ->disabled(function () use($timesheet) {
                            
                    if(isset($timesheet->time_in)){
                        
                        if($timesheet->time_in == '00:00:00'){
                            // did not time-in yet
                            return true;
                        }

                        if($timesheet->time_out != '00:00:00'){
                            return true;
                        }
                    }

                    return false;
                })  
                ->action(function (array $data) use ($employee, $current_date, $current_time, $timesheet) {
                    $location = $data['location'];

                    if ($employee) {
                        $result = $employee->employee_timelogs()->create([
                            'date' => $current_date,
                            'day' => now()->format('l'),
                            'type' => 'TIMEOUT',
                            'time' => $current_time,
                            'location' => $location
                        ]);

                        if ($result) {
                            if ($timesheet) {
                                $timesheet->time_out = $current_time;
                                $timesheet->out_location = $location;
                                $timesheet->out_date = $current_date;

                                // WILL MOVE THIS TO A JOB OR SCHEDULER
                                    // ALSO WILL ASKED IF TIME_IN LATE IS IMPORTANT OR TIME_OUT_LATE
                                    // Calculate late time for time_in and early leave time for time_out
                                    
                                    // ALSO CONSIDER THE SHIFT SCHEDULE 8am to 5pm, 8pm to 5am, 10pm to 8am etc.
                                    // CONSIDER WHAT WILL HAPPEN IF SHIFT WILL BE UPDATED.

                                    $shiftSchedule = explode(' - ', $timesheet->shift_schedule);
                                    $shiftStart = Carbon::parse($shiftSchedule[0]);
                                    $shiftEnd = Carbon::parse($shiftSchedule[1]);

                                    $timeIn = Carbon::parse($timesheet->time_in);
                                    $timeOut = Carbon::parse($current_time);

                                    $lateTimeInMinutes = 0;
                                    if ($timeIn->greaterThan($shiftStart)) {
                                        $lateTimeInMinutes = $shiftStart->diffInMinutes($timeIn);
                                    }

                                    $earlyLeaveMinutes = 0;
                                    if ($timeOut->lessThan($shiftEnd)) {
                                        $earlyLeaveMinutes = $timeOut->diffInMinutes($shiftEnd);
                                    }

                                    // Sum late arrival and early leave times
                                    $totalLateMinutes = $lateTimeInMinutes + $earlyLeaveMinutes;
                                    $timesheet->late_time = gmdate('H:i:s', $totalLateMinutes * 60);
                                // WILL MOVE THIS TO A JOB OR SCHEDULER

                                $timesheet->save();

                                Notification::make()
                                    ->success()
                                    ->title('Attendance')
                                    ->body('Time out ' . date('h:i A', strtotime($current_time)) . ' successfully.')
                                    ->send();
                            }
                        }
                    }
                })
                ->icon('heroicon-s-clock')
                ->tooltip('Do you want to Time out!'),
            ])  
            ->footerActionsAlignment(Alignment::Center)                          
        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']);
    }
    
}
