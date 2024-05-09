<?php

namespace App\Filament\Pages;

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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
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

            Grid::make([
                'default' => 1])
            ->schema([
                Split::make([
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        Split::make([
                            static::timeInSection($employee, $current_date, $current_time, $timesheet),
                            static::timeOutSection($employee, $current_date, $current_time, $timesheet),
                        ])->from('lg'),
                    ]),
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        Section::make('')
                        ->schema([
                            Fieldset::make('Clock')
                            ->schema([
                                
                            ])
                            ->extraAttributes(['class' => 'text-center'])
                            ->columns(1)
                        ])->extraAttributes(['class' => 'flex justify-center items-center text-center'])
                        ->columns(1),
                        
                        Section::make('Employee on-leave')
                        ->schema([
                            Fieldset::make('Table')
                            ->schema([
                                
                            ])
                            ->extraAttributes(['class' => 'text-center'])
                            ->columns(1)
                        ]),
                        Section::make('HR Announcement')
                        ->schema([
                            Fieldset::make('TABLE')
                            ->schema([
                                
                            ])
                            ->extraAttributes(['class' => 'text-center'])
                            ->columns(1)
                        ])
                    ])
                    ->extraAttributes(['class' => 'bg-gray-600'])
                    ->columns(1)
                    ->grow(false),                        
                ])->from('lg'),
            ])

        ]);
    }

    
    public static function timeInSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make()
        ->description()
        ->schema([
            Fieldset::make('Time-In')
            ->schema([    
                    Section::make('')
                    ->icon('heroicon-o-clock')
                    ->id('Date and Time in')
                    ->schema([
                        Placeholder::make('time')->label('Time')
                        ->content(function () use($timesheet) {

                            if(isset($timesheet)){
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
                                Select::make('location')
                                ->options([
                                    'OFFICE' => 'Office',
                                    'ONFIELD' => 'Onfield',
                                    'WFH' => 'Work From Home',
                                ])->required(),   
                            ])
                            ->disabled(function () use($timesheet) {
                                
                                if(isset($timesheet)){
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
                                                'shift_schedule' => $schedule
                                            ]);
                                        }

                                        
                                        Notification::make()
                                        ->success()
                                        ->title('Attendace')
                                        ->body('Time in '.$current_time.' successfully.')
                                        ->send();
                                    }
                                }
                            })
                            ->icon('heroicon-o-clock')
                            ->tooltip('Time in!'),
                    ])  
                    ->footerActionsAlignment(Alignment::Center)    
            ]),
        ]);
    } 

    public static function timeOutSection($employee, $current_date, $current_time, $timesheet): Section
    {
        return Section::make()
        ->schema([
            Fieldset::make('Time-Out')
            ->schema([
                Section::make('')
                ->icon('heroicon-s-clock')
                ->id('Date and Time out')
                ->schema([
                    Placeholder::make('time')->label('Time')
                    ->content(function () use($timesheet) {

                        if(isset($timesheet)){
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
                        Select::make('location')
                        ->options([
                            'OFFICE' => 'Office',
                            'ONFIELD' => 'Onfield',
                            'WFH' => 'Work From Home',
                        ])->required(),   
                    ])
                    ->disabled(function () use($timesheet) {
                                
                        if(isset($timesheet)){
                            if($timesheet->time_out != '00:00:00'){
                                return true;
                            }
                        }

                        return false;
                    })  
                    ->action(function (array $data) use ($employee, $current_date ,$current_time, $timesheet) {

                        $location = $data['location'];

                        if($employee){

                            $result = $employee->employee_timelogs()->create([
                                    'date' => $current_date,
                                    'day' => now()->format('l'),
                                    'type' => 'TIMEOUT',
                                    'time' => $current_time,
                                    'location' => $location
                            ]);

                            if($result) {
                                
                                if ($timesheet) {
                                    
                                    $timesheet->time_out = $current_time;
                                    $timesheet->out_location = $location;
                                    $timesheet->out_date = $current_date;
                                    $timesheet->save();

                                    Notification::make()
                                    ->success()
                                    ->title('Attendace')
                                    ->body('Time out '.$current_time.' successfully.')
                                    ->send();
                                }
                            }
                        }
                    })
                    ->icon('heroicon-s-clock')
                    ->tooltip('Time out!'),
                ])  
                ->footerActionsAlignment(Alignment::Center)                          
            ])
        ]);
    }
    
}
