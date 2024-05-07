<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\User;
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

class Dashboard extends BaseDashboard implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    use InteractsWithForms;
    // protected static string $view = 'filament.pages.dashboard';
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
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Split::make([
                        Fieldset::make('Time-In')
                        ->schema([    
                            // Actions::make([
                                // FormAction::make('time_in')
                                // ->label('Time in')
                                // // ->icon('heroicon-o-clock')
                                // ->tooltip('Time in!')
                                // ->form([
                                //     Select::make('Location')
                                //     ->options([
                                //         'OFFICE' => 'Office',
                                //         'ONFIELD' => 'Onfield',
                                //         'WFH' => 'Work From Home',
                                //     ])->required(),   
                                // ])
                                // // ->iconButton()
                                // ->action(function(array $data) {
                                //     $user = auth()->id();
                                //     dd($data);
                                // }),
                            // ]),                         

                                Section::make('')
                                ->icon('heroicon-o-clock')
                                ->id('Date and Time')
                                ->schema([
                                    Placeholder::make('Date and Time')
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
                                        ->action(function (array $data) {

                                            $user_id = auth()->id();
                                            $employee = Employee::where('user_id',$user_id)->first();

                                            $current_date = now()->toDateString();
                                            $current_time = now()->format('H:i');
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
                                                    
                                                    $timesheet = $employee->employee_timesheets()->where('date', $current_date)->first();
                                                    if ($timesheet) {
                                                        
                                                        $timesheet->time_in = $current_time;
                                                        $timesheet->in_location = $location;
                                                        $timesheet->save();
                                                    }
                                                }
                                            }
                                        })
                                        ->tooltip('Time in!'),
                                        // ->successNotification(
                                        //     Notification::make()
                                        //          ->success()
                                        //          ->title('Attendace')
                                        //          ->body('Time in successfully.'),
                                        //  )
                                ])  
                                ->footerActionsAlignment(Alignment::Center)    
                        ]),
                      
                        Fieldset::make('Time-Out')
                        ->schema([
                            // Actions::make([
                            //     FormAction::make('Time out')
                            //     ->tooltip('Time out!')
                            //     // ->icon('heroicon-s-clock')
                            //     // ->iconButton()
                            //     ->form([
                            //         Select::make('Location')
                            //         ->options([
                            //             'OFFICE' => 'Office',
                            //             'ONFIELD' => 'Onfield',
                            //             'WFH' => 'Work From Home',
                            //         ])->required(),
                            //     ])
                            //     ->disabled()
                            //     ->action(function() {
        
                            //     })
                            // ]),
                             
                            Section::make('')
                            ->icon('heroicon-s-clock')
                            ->id('Date and Time')
                            ->schema([
                                Placeholder::make('Date and Time')
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
                                ->action(function (array $data) {

                                    $user_id = auth()->id();
                                    $employee = Employee::where('user_id',$user_id)->first();

                                    $current_date = now()->toDateString();
                                    $current_time = now()->format('H:i');
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
                                            
                                            $timesheet = $employee->employee_timesheets()->where('date', $current_date)->first();
                                            if ($timesheet) {
                                                
                                                $timesheet->time_out = $current_time;
                                                $timesheet->out_location = $location;
                                                $timesheet->out_date = $current_date;
                                                $timesheet->save();
                                            }
                                        }
                                    }
                                })->tooltip('Time out!'),
                            ])  
                            ->footerActionsAlignment(Alignment::Center)                          
                        ])
                    ])->from('lg')
                ])
            ]);
    }
}
