<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EmployeeResource\Pages\TodayBirthdayView;
use App\Filament\Resources\LeaveResource\Widgets\OnLeaveCalendarWidget;
use App\Filament\Resources\TimesheetResource\Pages\EmployeeTimeLogsPage;
use App\Livewire\AnnouncementDashboardTable;
use App\Livewire\EmployeeNoticeBoardTable;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
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
                    Tabs::make('Tabs')
                    ->tabs([
                        isset($employee) ? Tab::make("Clock IN/OUT")
                        ->icon('heroicon-o-clock')
                        ->schema([
                            static::ClockInOutSection($employee, $current_date, $current_time, $timesheet)
                        ]) 
                        : Tab::make("Current user : ADMIN")
                        ->schema([

                        ])
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
                    Section::make('Your Notice Board')
                    ->schema([
                        Livewire::make(EmployeeNoticeBoardTable ::class)->key(self::generateUuid())
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
                            ->where('status', 'approved')
                            ->where(function ($query) {
                                $query->whereDate('from', '<=', now()->format('Y-m-d'))
                                    ->whereDate('to', '>=', now()->format('Y-m-d'));
                            })
                            ->count() . ' Today';
                        })
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            // having multiple Livewire error on uncaugth snapshot
                            // try every Livewire to be on a form or Component.
                            Livewire::make(OnLeaveCalendarWidget::class)->key(self::generateUuid())
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make("Celebrating Birthday's")
                        ->badge(function () {
                            $today = Carbon::today()->format('m-d');
                            return Employee::whereRaw('DATE_FORMAT(birthdate, "%m-%d") = ?', [$today])->get()
                            ->count();
                        })
                        ->icon('heroicon-o-cake')
                        ->schema([
                            // If you are rendering multiple of the same Livewire component, please make sure to pass a unique key() to each:
                            Livewire::make(TodayBirthdayView::class)->key(self::generateUuid())
                        ])->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                        Tab::make('Company News & Announcement')
                        ->badge(function () {
                            return Announcement::query()
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

    public static function ClockInOutSection($employee, $current_date, $current_time, $timesheet): Section
    {
        // Determine if the employee has already clocked in
        $hasClockedIn = isset($timesheet->time_in) && $timesheet->time_in != '00:00:00';
        $hasClockedOut = isset($timesheet->time_out) && $timesheet->time_out != '00:00:00';
    
        // Choose action based on clock-in status
        $action = $hasClockedIn
            ? static::createClockOutAction($employee, $current_date, $current_time, $timesheet,$hasClockedOut)
            : static::createClockInAction($employee, $current_date, $current_time, $timesheet);
    
        return Section::make()
            ->id('Time Logs')
            ->schema([
                Livewire::make(EmployeeTimeLogsPage::class)->data(['record' => $employee->employee_id])->key(self::generateUuid()),
            ])
            ->footerActions([
                $action,
            ])
            ->footerActionsAlignment(Alignment::Center)
            ->extraAttributes(['style' => 'box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']);
    }
    
    private static function createClockOutAction($employee, $current_date, $current_time, $timesheet, $hasClockedOut): FormAction
    {
        return FormAction::make('clock_out')
            ->label('PM - CLOCK OUT')
            ->form([
                Placeholder::make('Are you sure you want to time out?'),
                Select::make('location')
                    ->options([
                        'OFFICE' => 'Office',
                        'ONFIELD' => 'Onfield',
                        'WFH' => 'Work From Home',
                    ])
                    ->required(),
            ])
            ->disabled(function () use ($timesheet, $hasClockedOut) {
                return !$timesheet || $hasClockedOut;
            })
            ->action(function (array $data) use ($employee, $current_date, $current_time, $timesheet) {
                static::processClockOut($employee, $current_date, $current_time, $data['location'], $timesheet);
            })
            ->icon('heroicon-s-clock')
            ->tooltip('Do you want to Clock out!');
    }
    
    private static function createClockInAction($employee, $current_date, $current_time, $timesheet): FormAction
    {
        return FormAction::make('clock_in')
            ->label('AM - CLOCK IN')
            ->form([
                Placeholder::make('Do you want to time in?'),
                Select::make('location')
                    ->options([
                        'OFFICE' => 'Office',
                        'ONFIELD' => 'Onfield',
                        'WFH' => 'Work From Home',
                    ])
                    ->required(),
            ])
            ->disabled(function () use ($timesheet) {
                return isset($timesheet->time_in) && $timesheet->time_in != '00:00:00';
            })
            ->action(function (array $data) use ($employee, $current_date, $current_time, $timesheet) {
                static::processClockIn($employee, $current_date, $current_time, $data['location'], $timesheet);
            })
            ->icon('heroicon-o-clock')
            ->tooltip('Do you want to Clock in!');
    }
    
    private static function processClockOut($employee, $current_date, $current_time, $location, $timesheet)
    {
        if ($employee) {
            $result = $employee->employee_timelogs()->create([
                'date' => $current_date,
                'day' => now()->format('l'),
                'type' => 'TIMEOUT',
                'time' => $current_time,
                'location' => $location,
            ]);
    
            if ($result && $timesheet) {
                $timesheet->time_out = $current_time;
                $timesheet->out_location = $location;
                $timesheet->out_date = $current_date;
    
                // Calculate late and early leave time
                static::calculateLateAndEarlyLeave($timesheet, $current_time);
    
                $timesheet->save();
    
                Notification::make()
                    ->success()
                    ->title('Attendance')
                    ->body('Time out ' . date('h:i A', strtotime($current_time)) . ' successfully.')
                    ->send();
            }
        }
    }
    
    private static function processClockIn($employee, $current_date, $current_time, $location, $timesheet)
    {
        if ($employee) {
            $result = $employee->employee_timelogs()->create([
                'date' => $current_date,
                'day' => now()->format('l'),
                'type' => 'TIMEIN',
                'time' => $current_time,
                'location' => $location,
            ]);
    
            if ($result) {
                if ($timesheet) {
                    $timesheet->time_in = $current_time;
                    $timesheet->in_location = $location;
                    $timesheet->save();
                } else {
                    $schedule = static::generateShiftSchedule($employee);
                    $employee->employee_timesheets()->create([
                        'date' => $current_date,
                        'shift_schedule' => $schedule,
                        'time_in' => $current_time,
                        'in_location' => $location,
                    ]);
                }
    
                Notification::make()
                    ->success()
                    ->title('Attendance')
                    ->body('Time in ' . date('h:i A', strtotime($current_time)) . ' successfully.')
                    ->send();
            }
        }
    }
    
    private static function calculateLateAndEarlyLeave($timesheet, $current_time)
    {
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
    
        $lateTimeInMinutes = $timeIn->greaterThan($shiftStart) ? $shiftStart->diffInMinutes($timeIn) : 0;
        $earlyLeaveMinutes = $timeOut->lessThan($shiftEnd) ? $timeOut->diffInMinutes($shiftEnd) : 0;
                                
        // Sum late arrival and early leave times
        $totalLateMinutes = $lateTimeInMinutes + $earlyLeaveMinutes;
        $timesheet->late_time = gmdate('H:i:s', $totalLateMinutes * 60);
        $timesheet->save();

        // WILL MOVE THIS TO A JOB OR SCHEDULER
    }
    
    private static function generateShiftSchedule($employee)
    {
        $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
        $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
        return $time_in . ' - ' . $time_out;
    }
}
