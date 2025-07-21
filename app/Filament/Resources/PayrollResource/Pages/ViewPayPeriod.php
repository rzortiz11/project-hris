<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Livewire\EditPayPeriodForm;
use App\Livewire\ViewEmployeePayrollTable;
use App\Models\Employee;
use App\Models\EmployeeSalaryDetail;
use App\Models\Payroll;
use App\Models\PayrollAllowance;
use App\Models\PayrollBonus;
use App\Models\PayrollDeduction;
use App\Models\PayrollEmployeeContribution;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;

class ViewPayPeriod extends ViewRecord
{
    protected static string $resource = PayrollResource::class;

    public $isPayPeriodView = false;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        $record = $this->record;
        $actions = [];
                
        $actions[] = Action::make('generate_payroll')
        ->color('warning')
        ->label('Create Payroll')
        ->icon('heroicon-s-document-text')
        ->iconPosition(IconPosition::Before)
        ->form([
            Grid::make()
            ->schema([
                Group::make()
                ->schema([
                    Section::make('Pay Period')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->icon('heroicon-s-document-minus')
                    ->schema([
                        Grid::make()
                        ->schema([
                            Group::make([
                                DatePicker::make('cut_off')
                                ->default(function () use($record){
                                    return $record->cut_off_date;
                                })
                                ->readOnly()
                                ->required()
                                ->label('Cut Off Date')
                                ->suffixIcon('heroicon-o-calendar-days'),
                            ]),
                            Group::make([
                                DatePicker::make('cut_off_from')
                                ->default(function () use($record){
                                    return $record->start_date;
                                })
                                ->required()
                                ->readOnly()
                                ->label('From')
                                ->suffixIcon('heroicon-o-calendar-days'),
                                DatePicker::make('cut_off_to')
                                ->default(function () use($record){
                                    return $record->end_date;
                                })
                                ->readOnly()
                                ->required()
                                ->label('To')
                            ->suffixIcon('heroicon-o-calendar-days'),   
                            ])->columns(2),
                        ])->columns(1),
                    ])->columns(1),
                ])  
                ->columnSpan(1),    
                Group::make()
                ->schema([
                    Section::make('Employee Details')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->icon('heroicon-s-document-minus')
                    ->schema([
                        Grid::make()
                        ->schema([
                            Group::make([
                                Select::make('employee')->searchable()
                                ->options(User::all()->pluck('name','user_id')->map(function ($name) {
                                    return ucwords(strtolower($name));
                                })->toArray())
                                ->searchable()
                                ->required()
                                ->afterStateUpdated(function (Get $get, Set $set) use ($record){
                                    $user_id = $get('employee');
                                    $user = User::find($user_id);
                                
                                    $fullname = $user->first_name ." ".$user->last_name;
                                    $set('fullname',$fullname);
                                    $set('job_position', $user->employee->position->job_position ?? null);
                                    $set('reporting_designation', $user->employee->position->reporting_designation ?? null);
                                    $set('company', $user->employee->employment->company ?? null);
                                    $set('location', $user->employee->position->location ?? null);
        
                                    if(isset($user->employee->salary)){
                                        $basic_salary = $user->employee->salary()
                                        ->where('type', 'basic')
                                        ->first();
                                    }

                                    $basic_salary_amount = isset($basic_salary) ? $basic_salary->amount : 0;
                                    
                                    $set('basic_salary', $basic_salary_amount);
                                    
                                    if ($record->type == "biweekly") {
                                        $set('basic_salary_per_cutoff', $basic_salary_amount / 2);
                                    } else {
                                        $set('basic_salary_per_cutoff', $basic_salary_amount);
                                    }
    
                                    // Assuming $record->start_date and $record->end_date are Carbon instances
                                    $start_date = Carbon::parse($record->start_date);
                                    $end_date = Carbon::parse($record->end_date);
    
                                    // Calculate the total number of days (day range)
                                    $day_range = $start_date->diffInDays($end_date) + 1; // +1 to include both start and end dates
                                    $set('day_range', $day_range);
    
                                    // Calculate working days (excluding weekends)
                                    $working_days = 0;
                                    $current_date = $start_date->copy(); // Clone the start date to avoid modifying the original
    
                                    while ($current_date <= $end_date) {    
                                        // Check if the current day is a weekday (1 = Monday, 5 = Friday)
                                        if ($current_date->isWeekday()) {
                                            $working_days++;
                                        }
                                        $current_date->addDay(); 
                                    }

                                    // $set('allowances', [
                                    //     ['type' => '', 'amount' => ''],
                                    //     ['type' => '', 'amount' => ''],
                                    // ]);
    
                                    $set('working_days', $working_days);
                                    $set('total_gross_pay',1000);
                                    $set('other_deductions',100);
                                    $set('taxable_income',100);
                                    $set('income_tax_withheld',100);
                                    $set('cash_advance',100);
                                    $set('adjustment',100);
                                    $set('total_net_pay',1000);
        
                                })->live(),
                                TextInput::make('fullname')
                                ->required()
                                ->readOnly(),
                                TextInput::make('company')
                                ->required()
                                ->readOnly(),
                            ])->columns(3),
                            Group::make([
                                TextInput::make('job_position')
                                ->required()
                                ->readOnly(),
                                TextInput::make('reporting_designation')
                                ->required()
                                ->readOnly(),
                                TextInput::make('location')
                                ->required()
                                ->readOnly(),  
                            ])->columns(3),
                        ])->columns(1),
                    ]),
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
            Grid::make()
            ->schema([
                Group::make()
                ->schema([
                    Section::make('')
                    ->schema([
                        Group::make()
                        ->schema([
                            TextInput::make('day_range')->label('Day Range')->default(0)->inlineLabel(),
                            TextInput::make('working_days')->label('Working Days')->default(0)->inlineLabel(),
                            TextInput::make('absent')->default(0)->inlineLabel(),
                            // TextInput::make('over_time_hours')->default(0)->inlineLabel(),// i think this is equal to regular_work_overtime
                            TextInput::make('time_change_hours')->default(0)->inlineLabel(),
                            TextInput::make('under_time_hours')->default(0)->inlineLabel(),
                            TextInput::make('retro_hours')->default(0)->inlineLabel(),
                        ])->columns(3),
                    ])->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Additional Work Hours')
                    ->collapsible()
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->schema([
                        Split::make([
                            TextInput::make('regular_overtime_hours')->default(0),
                            Fieldset::make('Rest Day')
                            ->schema([
                                TextInput::make('rest_day_hours')->label('Hours')->default(0)->inlineLabel(),
                                TextInput::make('rest_day_overtime_hours')->label('Over Time')->default(0)->inlineLabel(),
                            ])->columns(1),
                            Fieldset::make('Legal Holiday')
                            ->schema([
                                TextInput::make('legal_holiday_hours')->label('Hours')->default(0)->inlineLabel(),
                                TextInput::make('legal_holiday_overtime_hours')->label('Over Time')->default(0)->inlineLabel(),
                            ])->columns(1),
                            Fieldset::make('Special Holiday')
                            ->schema([
                                TextInput::make('special_holiday_hours')->label('Hours')->default(0)->inlineLabel(),
                                TextInput::make('special_holiday_overtime_hours')->label('Over Time')->default(0)->inlineLabel(),
                            ])->columns(1),
                        ])
                    ])->columns(1),
                    Section::make('')
                    ->schema([
                        Split::make([
                            Fieldset::make('Leave With Pay')    
                            ->schema([
                                TextInput::make('leave')->label('Days')->default(0),
                                TextInput::make('leave_hours')->label('Hours')->default(0),
                            ]),
                            Fieldset::make('Late')
                            ->schema([
                                TextInput::make('late_days')->label('Days')->default(0),
                                TextInput::make('late_hours')->label('Hours')->default(0),
                            ]), 
                        ]),
                    ])->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Allowances')
                    ->schema([
                        Group::make([
                            Repeater::make('allowances')
                                ->defaultItems(0)
                                ->label('')
                                ->schema([
                                    Select::make('employee_salary_id')->label('Type')
                                    ->required()
                                    ->inlineLabel()
                                    ->options(function (Get $get) {
                                        $userId = $get('../../employee');
                                        $user = User::find($userId);
                                        if ($user && isset($user->employee)) {
                                            return EmployeeSalaryDetail::where('employee_id', $user->employee->employee_id)
                                                ->where('type', 'allowance')
                                                ->pluck('name', 'employee_salary_id')
                                                ->toArray();
                                        }
                                        return [];
                                    })
                                    ->disabled(fn(Get $get) : bool => ! filled($get('../../employee')))
                                    ->afterStateUpdated(function (Get $get, Set $set) use ($record){
                                       $salary_detail = EmployeeSalaryDetail::find($get('employee_salary_id'));
                                       $set('amount', $salary_detail->amount);
                                       $set('name', $salary_detail->name);
                                       $set('is_taxable', $salary_detail->is_taxable ? true : false);

                                       //after state update update the allowance field
                                    })
                                    ->live()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                    TextInput::make('name')
                                    ->hidden(true)
                                    ->readOnly(),
                                    TextInput::make('amount')
                                    ->inlineLabel()
                                    ->label('Amount')
                                    ->readOnly(),
                                    Checkbox::make('is_taxable')->inline()
                                ])
                                ->reorderable(false)
                                ->columns(3)
                        ])->columnSpanFull()
                    ])
                    ->collapsible()
                    ->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Deductions')
                    ->schema([
                        Group::make([
                            Repeater::make('deductions')
                                ->defaultItems(0)
                                ->label('')
                                ->schema([
                                    TextInput::make('name')
                                    ->required()
                                    ->inlineLabel(),
                                    TextInput::make('amount')
                                    ->afterStateUpdated(function (Get $get, Set $set) use ($record){
                                       //after state update the deduction field
                                    })
                                    ->live(debounce: 500)
                                    ->inlineLabel()
                                    ->label('Amount'),
                                ])
                                ->reorderable(false)
                                ->columns(2)
                        ])->columnSpanFull()
                    ])->columns(1)
                    ->collapsible()
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Bonuses')
                    ->schema([
                        Group::make([
                            Repeater::make('bonuses')
                                ->defaultItems(0)
                                ->label('')
                                ->schema([
                                    Select::make('employee_salary_id')->label('Type')
                                    ->required()
                                    ->inlineLabel()
                                    ->options(function (Get $get) {
                                        $userId = $get('../../employee');
                                        $user = User::find($userId);
                                
                                        if ($user && isset($user->employee)) {
                                            return EmployeeSalaryDetail::where('employee_id', $user->employee->employee_id)
                                                ->where('type', 'bonuses')
                                                ->pluck('name', 'employee_salary_id')
                                                ->toArray();
                                        }
                                        return [];
                                    })
                                    ->disabled(fn(Get $get) : bool => ! filled($get('../../employee')))
                                    ->afterStateUpdated(function (Get $get, Set $set) use ($record){
                                       $salary_detail = EmployeeSalaryDetail::find($get('employee_salary_id'));
                                       $set('amount', $salary_detail->amount);
                                       $set('name', $salary_detail->name);
                                       $set('is_taxable', $salary_detail->is_taxable ? true : false);

                                       //after state update the bonuses field
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                    TextInput::make('name')
                                    ->hidden(true)
                                    ->readOnly(),
                                    TextInput::make('amount')
                                    ->inlineLabel()
                                    ->label('Amount')
                                    ->readOnly(),
                                    Checkbox::make('is_taxable')->inline()
                                ])
                                ->reorderable(false)
                                ->columns(3)
                        ])->columnSpanFull()
                    ])
                    ->collapsible()
                    ->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Contribution')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->icon('heroicon-s-document-minus')
                    ->schema([
                        Split::make([
                            Grid::make()
                            ->schema([
                                Checkbox::make('with_sss')->label('with SSS')
                                ->afterStateUpdated(function (Get $get, Set $set){
                                    $set('sss',100);
                                    $set('employee_sss',100);
                                })->live(),
                                Checkbox::make('with_pag_ibig')->label('with Pag-Ibig')
                                ->afterStateUpdated(function (Get $get, Set $set){
                                    $set('pag_ibig',200);
                                    $set('employee_pag_ibig',200);
                                })->live(),
                                Checkbox::make('with_philhealth')->label('with Philhealth')
                                ->afterStateUpdated(function (Get $get, Set $set){
                                    $set('philhealth',300);
                                    $set('employee_philhealth',300);
                                })->live(),
                            ])
                            ->grow(false)
                            ->columns(1),
                            Grid::make()
                            ->schema([
                                Fieldset::make('Mandatory Contribution')
                                ->schema([
                                    TextInput::make('sss')->label('SSS')->default(0)->inlineLabel(),
                                    TextInput::make('pag_ibig')->label('Pag-Ibig')->default(0)->inlineLabel(),
                                    TextInput::make('philhealth')->label('Philhealth')->default(0)->inlineLabel(),
                                ])->columns(1)
                            ]),
                            Grid::make()
                            ->schema([
                                Fieldset::make('Employer Contribution')
                                ->schema([
                                    TextInput::make('employee_sss')->label('SSS')->default(0)->inlineLabel(),
                                    TextInput::make('employee_pag_ibig')->label('Pag-Ibig')->default(0)->inlineLabel(),
                                    TextInput::make('employee_philhealth')->label('Philhealth')->default(0)->inlineLabel(),
                                ])->columns(1),
                            ]),
                        ])
                        ->from('lg'),
                    ])->columns(1),
                ])
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 2,
                    'xl' => 2,
                    '2xl' => 2,
                ]),
                Group::make()
                ->schema([
                    Section::make('')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->schema([
                        TextInput::make('basic_salary')->label('Basic Salary')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('basic_salary_per_cutoff')->prefix('₱ ')->default(0)->inlineLabel()->label('Salary Per Cutoff'),
                        TextInput::make('leave_pay')->prefix('₱ ')->default(0)->inlineLabel(), // check if i need to add this or not
                        TextInput::make('over_time_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('holiday_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('retro_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('allowances_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('bonuses_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('total_gross_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('late_deductions')->prefix('₱ ')->default(0)->inlineLabel(),// check if i need to add this or not
                        TextInput::make('other_deductions')->prefix('₱ ')->default(0)->inlineLabel(),
                        TextInput::make('taxable_income')->default(0)->inlineLabel(),
                        TextInput::make('income_tax_withheld')->default(0)->inlineLabel(),
                        TextInput::make('cash_advance')->default(0)->inlineLabel(),
                        TextInput::make('adjustment')->default(0)->inlineLabel(),
                        TextInput::make('total_net_pay')->prefix('₱ ')->default(0)->inlineLabel(),
                    ])->columns(1),
                    Section::make('')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->schema([
                        Textarea::make('remarks')->label('Remarks')
                        ->placeholder('Enter Remarks...')
                        ->minLength(2)
                        ->maxLength(255)
                        ->autosize()
                        ->rows(5),
                    ])->columns(1)
                ])  
                ->columnSpan(1),    
            ])
            ->columns(3),
        ])->modalWidth('8xl')
        ->action(function ($data) use ($record) {
            $user = User::where('user_id',$data['employee'])->first();

            $post_data = [
                'pay_period_id' => $record->pay_period_id,
                'employee_id' => $user->employee->employee_id,
                'fullname' => $data['fullname'],
                'job_position' => $data['job_position'],
                'reporting_designation' => $data['reporting_designation'],
                'location' => $data['location'],
                'company' => $data['company'],
                'cut_off' => $data['cut_off'],
                'cut_off_from' => $data['cut_off_from'],
                'cut_off_to' => $data['cut_off_to'],
                'day_range' => $data['day_range'],
                'working_days' => $data['working_days'],
                'regular_overtime_hours' => $data['regular_overtime_hours'],
                'rest_day_hours' => $data['rest_day_hours'],
                'rest_day_overtime_hours' => $data['rest_day_overtime_hours'],
                'legal_holiday_hours' => $data['legal_holiday_hours'],
                'legal_holiday_overtime_hours' => $data['legal_holiday_overtime_hours'],
                'special_holiday_hours' => $data['special_holiday_hours'],
                'special_holiday_overtime_hours' => $data['special_holiday_overtime_hours'],
                'absent' => $data['absent'],
                'late_days' => $data['late_days'],
                'late_hours' => $data['late_hours'],
                'leave_days' => $data['leave'],
                'leave_hours' => $data['leave_hours'],
                'time_change_hours' => $data['time_change_hours'],
                // 'over_time_hours' => $data['over_time_hours'],
                'under_time_hours' => $data['under_time_hours'],
                'retro_hours' => $data['retro_hours'],
                'basic_pay' => $data['basic_salary_per_cutoff'],
                'over_time_pay' => $data['over_time_pay'], //regular_overtime + rest_day_overtime
                'holiday_pay' => $data['holiday_pay'],
                'allowances_pay' => $data['allowances_pay'],
                'retro_pay' => $data['retro_pay'],
                'bonuses_pay' => $data['bonuses_pay'],
                'total_gross_pay' => $data['total_gross_pay'],
                'sss_contribution' => $data['sss'],
                'philhealth_contribution' => $data['philhealth'],
                'pagibig_contribution' => $data['pag_ibig'],
                'other_deductions' => $data['other_deductions'],
                'taxable_income' => $data['taxable_income'],
                'income_tax_withheld' => $data['income_tax_withheld'],
                'cash_advance' => $data['cash_advance'],
                'adjustment' => $data['adjustment'],
                'total_net_pay' => $data['total_net_pay'],
                'remarks' => $data['total_net_pay'],
                'created_by' => auth()->id()
            ];

            $result = Payroll::create(
                $post_data
            );

            if($result) {
                $payroll_id = $result->payroll_id;

                // add employee contribution
                if(isset($data['contribution'])){
                    PayrollEmployeeContribution::create([
                        'payroll_id' => $payroll_id,
                        'sss_contribution' => $data['employee_sss'],     
                        'philhealth_contribution' => $data['employee_philhealth'],     
                        'pagibig_contribution' => $data['employee_pag_ibig'],     
                    ]);
                }

                // add allowance
                if(isset($data['allowances'])){
                    foreach($data['allowances'] as $allowance) {

                        PayrollAllowance::create([
                            'payroll_id' => $payroll_id,
                            'name' => $allowance['name'],     
                            'is_taxable' => $allowance['is_taxable'],     
                            'amount' => $allowance['amount'],     
                        ]);
                    }
                };

                // add deduction
                if(isset($data['deductions'])){
                    foreach($data['deductions'] as $allowance) {

                        PayrollDeduction::create([
                            'payroll_id' => $payroll_id,
                            'name' => $allowance['name'],     
                            'amount' => $allowance['amount'],     
                        ]);
                    }
                };

                // add bonuses
                if(isset($data['bonuses'])){
                    foreach($data['bonuses'] as $allowance) {

                        PayrollBonus::create([
                            'payroll_id' => $payroll_id,
                            'name' => $allowance['name'],     
                            'is_taxable' => $allowance['is_taxable'],     
                            'amount' => $allowance['amount'],     
                        ]);
                    }
                };
            }

            if($result){
                // GENERATE PAYROLL/PAYSLIP PDF FILE.
                self::sendRequestNotification($user);
            }
        });

        $actions[] = Action::make('return')
        ->color('info')
        ->label('Return')
        ->action(function () {
            redirect()->route('filament.admin.resources.payrolls.index');
        })
        ->hidden($this->isPayPeriodView);

        return $actions;
    }

    public static function sendRequestNotification($recipient){

        // or should have an option to notify employee if payslip is available based on admin
        Notification::make()
            ->title('Payslip Generated')
            ->body('Your Payslip for the period of is available')
            ->icon('heroicon-o-bell-alert')
            ->warning()
            ->actions([
          
            ])
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Payslip Generated')
        ->icon('heroicon-o-bell-alert')
        ->body('Your Payslip for the period of is available')
        ->seconds(5)
        ->actions([
   
        ])
        ->warning()
        ->broadcast($recipient);
        
        Notification::make()
        ->title('Payroll Generated')
        ->body('Payroll for employee '. $recipient->name . " was succesfully generated." )
        ->seconds(5)
        ->success()
        ->send();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Pay Period Details')
                ->description('Cut Off Details')
                ->icon('heroicon-s-clock')
                ->schema([
                    Livewire::make(EditPayPeriodForm::class)->data(['record' => $this->record])->key(self::generateUuid())
                ])->columns(1),
                InfoSection::make("Payroll Table")
                ->schema([
                    // add a table here with create button to create payroll and table with editable column
                    Livewire::make(ViewEmployeePayrollTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                ])
            ]);
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }
}
