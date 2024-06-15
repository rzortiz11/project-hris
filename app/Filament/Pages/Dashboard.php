<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EmployeeResource\Pages\TodayBirthdayView;
use App\Filament\Resources\LeaveResource\Widgets\OnLeaveCalendarWidget;
use App\Filament\Resources\LeaveResource\Widgets\OnLeaveTodayCalendarWidget;
use App\Livewire\AnalogClock;
use App\Livewire\AnnouncementDashboardTable;
use App\Livewire\EmployeeOnLeaveTable;
use App\Livewire\EmployeeUpcomingLeaveTable;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Leave;
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
use Filament\Forms\Components\Group;
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
use Illuminate\Support\Str;

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
                Group::make()
                ->schema([
                    // Section::make('')
                    // ->schema([
                    //     Livewire::make(AnalogClock::class)
                    // ])
                    // ->extraAttributes([
                    //     'class' => 'flex justify-center items-center text-center',
                    //     'style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);margin-top: 75px;'
                    // ])
                    // ->columns(1),
                    Tabs::make('Tabs')
                    ->tabs([
                        // Tab::make('On Leave Today')
                        // ->badge(function () {
                        //     return Leave::query()
                        //     // add on leave today
                        //     ->where('status', 'approved')
                        //     ->count();
                        // })
                        // ->icon('heroicon-o-megaphone')
                        // ->schema([
                        //     Livewire::make(OnLeaveTodayCalendarWidget::class)
                        // ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make("Celebrating Birthday's")
                        ->badge(function () {
                            return Employee::query()
                            // add on birth today and query with birthday
                            // ->where('status', 'approved')
                            ->count();
                        })
                        ->icon('heroicon-o-cake')
                        ->schema([
                            // If you are rendering multiple of the same Livewire component, please make sure to pass a unique key() to each:
                            Livewire::make(TodayBirthdayView::class)->key(self::generateUuid())
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
                    Split::make([
                        static::timeInSection($employee, $current_date, $current_time, $timesheet),
                        static::timeOutSection($employee, $current_date, $current_time, $timesheet),
                    ])->from('lg'),
                    Section::make('Notice Board')
                    ->schema([
                    ])->extraAttributes([
                        'class' => ' justify-center items-center text-center',
                        'style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8)'
                    ])->columns(1),

                ])  
                ->columnSpan(1),    
                Group::make()
                ->schema([
                    Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('On Leave Calendar')
                        ->badge(function () {
                            return Leave::query()
                            // add on leave today and where status is approved
                            ->where('status', 'approved')
                            ->count();
                        })
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            // having multiple Livewire error on uncaugth snapshot
                            // try every Livewire to be on a form or Component.
                            Livewire::make(OnLeaveCalendarWidget::class)->key(self::generateUuid())
                            // will convert this to filament full calendar package set default to today with week and months.
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make('Company News & Announcement')
                        ->badge(function () {
                            return Announcement::query()
                            // add on leave today and where status is approved
                            ->count();
                        })
                        ->icon('heroicon-o-megaphone')
                        ->schema([
                            Livewire::make(AnnouncementDashboardTable::class)->key(self::generateUuid())
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
                ])
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 2,
                    'xl' => 2,
                    '2xl' => 2,
                ]),
            ])
            ->columns(3),
        ]);
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }

    
    public static function timeInSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make('Time in')
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
            ->extraAttributes([
                'style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'
            ]);   
    } 

    public static function timeOutSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make('Time out')
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
            ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']);         
    }
    
}
