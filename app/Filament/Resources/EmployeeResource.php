<?php

namespace App\Filament\Resources;

use App\Filament\Exports\EmployeeImporterExample;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Livewire\ViewSalaryDetails;
use App\Models\EmployeeFamilyDetail;
use App\Models\EmployeeManagement;
use App\Models\Nationality;
use App\Models\User;
use App\Models\UtilityBarangay;
use App\Models\UtilityCity;
use App\Models\UtilityDisctrict;
use App\Models\UtilityRegion;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions as formComponentACtion;
class EmployeeResource extends Resource
{
    protected static ?string $model = EmployeeManagement::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Employee Management';

    public static function form(Form $form): Form
    {
        //EmployeeManagement extends Employee Model for policy to work to hide this on user with employee ROLE
        $employee = EmployeeManagement::get();
        $model_record = $form->getRecord();
        
        return $form
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Split::make([
                        Grid::make([
                            'default' => 1
                        ])
                        ->schema([
                           Tabs::make()
                            ->tabs([
                               Tab::make('Basic Details')
                                    ->schema([
                                        static::PersonalInformation()
                                    ])->columns(2),
                               Tab::make('Contact Details')
                                    ->schema([
                                        static::contactAddressInformation(),
                                        static::contactInformation(),
                                        static::emergencyContactPersonInformation(),                   
                                    ]),     
                               Tab::make('Work Details')
                                    ->schema([
                                        static::employementInformation(),  
                                        Grid::make()
                                        ->schema([
                                            Group::make()
                                            ->schema([
                                                static::employementPositionInformation(),  
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
                                                static::employeeRequestApprover(),
                                            ])  
                                            ->columnSpan(1),    
                                        ])
                                        ->columns(3),
                                        static::issuedItemInformation(),  
                                    ]),    
                               Tab::make('Salary Details')                                
                                    ->schema([
                                        static::salaryInformation(),  
                                        static::payComponentInformation(),                                    
                                    ]),  
                               Tab::make('Family Details')                                
                                    ->schema([
                                        static::parentInformartion(),
                                        static::spouseInformation(),
                                        static::childrenInformation(),
                                    ]),           
                                Tab::make('Dependent & HMO')
                                ->schema([
                                        static::dependentAndhealthBenefitInformation($model_record),
                                ]),                                                                                                                                                                                 
                               Tab::make('Education & Work History')
                                    ->schema([
                                        static::educationAndWorkHistoryInformation(),
                                    ]),
                               Tab::make('Training Details')
                                ->schema([
                                    static::trainingInformation(),  
                                ]),
                                Tab::make('Gov ID & Bank Details')
                                ->schema([
                                    static::idInformation(),
                                    static::bankInformation()
                                ]),                                                                
                               Tab::make('Document Details')
                                ->schema([
                                    static::documentInformation()
                                ]),                                      
                            ])
                            ->contained(false)
                            ->columnSpanFull()
                            ->persistTabInQueryString()
                            // ->persistTab()
                            // ->id('basic-details-tab')
                        ]),
                        Grid::make([
                            'default' => 1
                        ])
                        ->schema([
                            static::profileDisplay(),
                        ])
                        ->extraAttributes(['class' => 'bg-gray-600'])
                        ->columns(1)
                        ->grow(false),
                        // Section::make()->schema([]),
                        // Section::make()->schema([])
                        // ->columns(1)
                        // ->grow(false),
                    ])->from('lg')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                TableAction::make('Import')
                ->label("Import Employee's")
                ->icon('heroicon-o-arrow-up-on-square')
                ->iconPosition(IconPosition::After)
                ->requiresConfirmation()
                ->form([
                    FileUpload::make('csv')
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain'])
                    ->required()
                    ->label('CSV File'),
                    formComponentACtion::make([
                        Action::make('export')
                        ->label('Download example CSV file')
                        ->link()
                        ->icon('heroicon-o-arrow-down-on-square')
                        ->action(function () {
                            return Excel::download(new EmployeeImporterExample(), 'employee-importer-example.csv');
                        }),
                    ]),
                    // ->fullWidth(),
                ])
                ->action(function ($data) {
                    $importer = new UserImporter();
                    Excel::import($importer, $data['csv'], 'public');

                    // $duplicates = $importer->getDuplicates();

                    Notification::make()
                    ->success()
                    ->title("Employee's imported successfully")
                    ->send();
                }),
            ])
            ->columns([
                TextColumn::make('employee_id')->label('ID'),
                ImageColumn::make('avatar')
                ->grow(false)
                ->getStateUsing(function (EmployeeManagement $data): string {

                    return isset($data->picture) ? $data->picture : '';
                })
                ->circular(),
                TextColumn::make('employee_reference')->searchable(),
                TextColumn::make('user.name')->label('Employee')->searchable(['first_name','last_name']),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('position.job_position')->label('Position'),
                TextColumn::make('position.reporting_designation')->label('Designation'),
                TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (EmployeeManagement $record): string {
                    return $record->is_active ? 'active': 'inactive';
                }),
                TextColumn::make('created_at')->label('Created Date and Time')               
                ->getStateUsing(function (EmployeeManagement $employee): string {

                    $created_at = Carbon::parse($employee->created_at);
                    return $created_at->format('Y-m-d H:i:s');
                })->searchable()                    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            // 'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function profileDisplay(): Grid
    {
        return Grid::make([
            'default' => 1
        ])
        ->schema([
            Section::make('')
            ->schema([
                FileUpload::make('picture')->label('')
                ->disk('public')
                ->visibility('private')
                ->directory('employe/picture')
                ->avatar()
                ->imageEditor()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                    '1:1',
                ]),
                Placeholder::make('Employee Name')->label('')
                ->content(fn (EmployeeManagement $record): ?string => $record ? $record->user->name : "")
                ->extraAttributes(['class' => 'text-xs']),
                Placeholder::make('Position')->label('')                                
                ->content(fn (EmployeeManagement $record): ?string => isset($record->position) ? $record->position->job_position : "N/A")
                ->extraAttributes(['class' => 'text-xs']),
                Placeholder::make('Employee Number')->label('')
                ->content(fn (EmployeeManagement $record): ?string => $record ? $record->employee_reference : "")
                ->extraAttributes(['class' => 'text-xs']),
            ])->extraAttributes(['class' => 'flex justify-center items-center text-center'])
            ->columns(1),
            
            Section::make('')
            ->schema([
                Fieldset::make('Profile Completion')
                ->schema([
                    Placeholder::make('')->content("0%"),
                    Placeholder::make('')->content("OVERALL PROFILE COMPLETION")->extraAttributes(['class' => 'text-xs']),
                ])
                ->extraAttributes(['class' => 'text-center'])
                ->columns(1)
            ])
        ]);
    }

    public static function PersonalInformation(): Section
    {
        return Section::make('PERSONAL INFORTMATION')
        ->description('Employee Personal Details')
        ->icon('heroicon-s-user-circle')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->relationship('user')
            ->schema([
                TextInput::make('first_name')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('middle_name'),
                TextInput::make('suffix'),
            ])->columns(4),
            Grid::make([
                'default' => 1
            ])
            ->relationship('user')
            ->schema([
                TextInput::make('mobile')
                ->suffixIcon('heroicon-o-device-phone-mobile')
                ->unique(ignoreRecord: true)
                ->required(),
                TextInput::make('email')
                ->email()
                ->suffixIcon('heroicon-o-envelope')
                ->default('')
                ->unique(ignoreRecord: true)
                ->placeholder('morepower.ph')
                ->readonly()
                ->required(),
            ])->columns(2),
            TextInput::make('title'),
            Split::make([
                DatePicker::make('birthdate')
                ->label('Date of Birth')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),
                Placeholder::make('age')
                ->content(function ($record) {
                    return static::getAge(isset($record->birthdate) ? $record->birthdate : "");
                }),
            ])->from('lg'),
            TextInput::make('religion'),
            Select::make('nationality')
            ->options(Nationality::getNationalities()->pluck('nationality', 'nationality')->toArray())
            ->default('Filipino')
            ->searchable()
            ->preload(),
            Select::make('gender')->options([
                'Male' => 'Male',
                'Female' => 'Female',
                'Others' => 'Others'
            ])
            ->required()
            ->preload(),
            Group::make([
                TextInput::make('biometric_id')->label('Biometric ID'),
                Placeholder::make('employee_reference')
                ->content(fn (EmployeeManagement $record): ?string => $record ? $record->employee_reference : ""),
            ])->columns(2),

        ])->columns(2);
    }

    public static function contactAddressInformation(): Section 
    {
        return Section::make('ADDRESS INFORTMATION')
        ->description('Employee Address Details')
        ->icon('heroicon-s-home-modern')
        ->schema([
            Repeater::make('addresses')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('street'),
                    TextInput::make('landmark'),  
                ])->columns(2),                                                
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('unit_no'),   
                    TextInput::make('bldg_floor'),   
                    TextInput::make('subdivision'),
                ])->columns(3),
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Select::make('region_id')->label('Region')
                    ->options(UtilityRegion::all()->pluck('name','utility_region_id')->map(function ($name) {
                        return ucwords(strtolower($name));
                    })->toArray())
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('city_id', '');
                        $set('district_id', '');
                        $set('barangay_id', '');
                    })
                    ->live(),
                    Select::make('city_id')->label('City')
                    ->searchable()
                    ->options(fn (Get $get): array => match ($get('region_id')) {
                        default => UtilityCity::where('utility_region_id', $get('region_id'))
                            ->pluck('name', 'utility_city_id')
                            ->map(fn ($name) => ucwords(strtolower($name)))
                            ->toArray(),
                    })
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('district_id', '');
                        $set('barangay_id', '');
                    })
                    ->live(),
                    Select::make('district_id')->label('District/Municipality')
                    ->searchable()
                    ->options(fn (Get $get): array => match ($get('city_id')) {
                        default => UtilityDisctrict::where('utility_city_id', $get('city_id'))
                            ->pluck('name', 'utility_district_id')
                            ->map(fn ($name) => ucwords(strtolower($name)))
                            ->toArray(),
                    })
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('barangay_id', '');
                    })
                    ->live(),
                    Select::make('barangay_id')->label('Barangay')
                    ->searchable()
                    ->options(fn (Get $get): array => match ($get('district_id')) {
                        default => UtilityBarangay::where('utility_district_id', $get('district_id'))
                            ->pluck('name', 'utility_barangay_id')
                            ->map(fn ($name) => ucwords(strtolower($name)))
                            ->toArray(),
                    })
                    ->live(),  
                ])->columns(2)
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['type'] == 'TEMPORARY') {
                    return 'TEMPORARY ADDRESS';
                } elseif ($state['type'] == 'PERMANENT') {
                    return 'PERMANENT ADDRESS';
                }
                return null;
            })
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->columns(1)
            ->grid(2)
        ])
        ->collapsed(false);
    }

    public static function contactInformation(): Section 
    {
        return  Section::make('CONTACT INFORMATION')
        ->description('Employee Contact Information')
        ->icon('heroicon-s-device-phone-mobile')
        ->schema([
            Placeholder::make('mobile')
            ->content(fn (EmployeeManagement $record): ?string => $record ? $record->user->mobile : ""),
            Placeholder::make('email')
            ->content(fn (EmployeeManagement $record): ?string => $record ? $record->user->email : ""),
            Grid::make([
                'default' => 1
            ])
            ->relationship('contact')
            ->schema([
                TextInput::make('secondary_mobile')
                ->suffixIcon('heroicon-o-device-phone-mobile'),
                TextInput::make('secondary_email')
                ->suffixIcon('heroicon-o-envelope')
                ->default('')
                ->email()
                ->placeholder('personal-email'),
                TextInput::make('telephone')
                ->suffixIcon('heroicon-o-phone'), 
                TextInput::make('secondary_telephone')
                ->suffixIcon('heroicon-o-phone'),                                               
                TextInput::make('facebook_profile'),   
                TextInput::make('linkedIn_profile')                                 
         
            ])->columns(2),
        ])
        ->collapsed(false)
        ->columns(2);
    }

    public static function emergencyContactPersonInformation(): Section 
    {
        return Section::make('EMERGENCY CONTACT PERSON INFORMATION')
        ->icon('heroicon-s-phone-arrow-down-left')
        ->description('Employee Emergency Contact Persons')
        ->schema([
            Repeater::make('emergencyContacts')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('name')->label('Full Name'),
                    Select::make('relationship')->label('Relationship to the employee'),   
                    TextInput::make('mobile')
                    ->suffixIcon('heroicon-o-phone'),
                    TextInput::make('telephone')
                    ->suffixIcon('heroicon-o-phone'),
                    TextInput::make('email')
                    ->suffixIcon('heroicon-o-envelope'),
                    TextInput::make('address')
                    ->suffixIcon('heroicon-o-map')
                ])->columns(2)
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['name']) {
                    return strtoupper('* INCASE OF EMERGENCY CONTACT - '. $state['name']).' *';
                } 
                return null;
            })
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->reorderable(false)
            ->columns(1)
        ])
        ->collapsed(false)
        ->columns(1);                     
    }

    public static function employementInformation(): Section
    {
        return Section::make('EMPLOYMENT DETAILS')
        ->description('Employee Employement Information')
        ->icon('heroicon-s-briefcase')
        ->schema([
            Split::make([
                Grid::make([
                    'default' => 1
                ])
                ->relationship('employment')
                ->schema([
                    Grid::make()
                    ->schema([
                        Group::make()
                        ->schema([
                            Grid::make([
                                'default' => 1
                            ])
                            ->schema([
                                Grid::make([
                                    'default' => 1
                                ])
                                ->schema([
                                    Select::make('employment_type')->options([
                                        'PROBATION' => 'Probation',
                                        'REGULAR' => 'Regular'
                                    ])
                                    ->required()
                                    ->preload(),
                                    Select::make('employment_category')->options([
                                        'PARTTIME' => 'Part-time',
                                        'FULLTIME' => 'Full-time',
                                        'THIRDPARTY' => 'Third-Party'
                                    ])
                                    ->required()
                                    ->preload(),
                                    Select::make('employment_status')->options([
                                        'EMPLOYED' => 'Employed',
                                        'TERMINATED' => 'Terminated',
                                        'RESIGNED' => 'Resigned',
                                        'SEPERATED' => "Seperated"
                                    ])
                                    ->required()
                                    ->preload(),
                                    Select::make('work_arrangement')->options([
                                        'ONSITE' => 'On-site',
                                        'WFH' => 'Work from home',
                                        'HYBRID' => 'Hybrid'
                                    ])
                                    ->required()
                                    ->preload(),
                                ])->columns(4),
                                Group::make([
                                    Select::make('company')
                                    ->required()
                                    ->options([
                                        'companya' => 'Company A',
                                        'companyb' => 'Company B',
                                    ]),
                                    Select::make('payment_structure')
                                    ->options([
                                        'COMPANY' => 'Company',
                                    ])
                                    ->required()
                                    ->searchable(),                                                    
                                    Select::make('payroll_cycle')->options([
                                        'BI-MONTHLY' => 'Bi-montly',
                                        'MONTHLY' => 'Monthly'
                                    ])->preload()
                                    ->required(),
                                ])->columns(3),
                                Group::make([
                                    DatePicker::make('employement_date')
                                    ->suffixIcon('heroicon-o-calendar-days')
                                    ->required(),
                                    DatePicker::make('probation_end_date')
                                    ->suffixIcon('heroicon-o-calendar-days'),
                                    DatePicker::make('termination_date')
                                    ->suffixIcon('heroicon-o-calendar-days'),
                                    DatePicker::make('seperation_date')
                                    ->suffixIcon('heroicon-o-calendar-days')
                                ])->columns(4),
                            ])->columns(1),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 3,
                            'xl' => 3,
                            '2xl' => 3,
                        ]),
                        Group::make()
                        ->schema([
                            Grid::make([
                                'default' => 1
                            ])
                            ->schema([
                                // CheckboxList::make('work_schedule')
                                // ->label("Work Schedule")
                                // ->options([
                                //     'monday'    => 'Monday',
                                //     'tuesday'   => 'Tuesday',
                                //     'wednesday' => 'Wednesday',
                                //     'thursday'  => 'Thursday',
                                //     'friday'    => 'Friday',
                                //     'saturday'  => 'Saturday',
                                //     'sunday'    => 'Sunday'
                                // ])
                                // ->required()
                                // ->columns(2)
                                // ->gridDirection('row'),
                                Select::make('work_schedule')
                                ->options([
                                    'monday'    => 'Monday',
                                    'tuesday'   => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday'  => 'Thursday',
                                    'friday'    => 'Friday',
                                    'saturday'  => 'Saturday',
                                    'sunday'    => 'Sunday'
                                ])
                                ->required()
                                ->multiple()
                            ])
                            ->columns(1),
                        ])  
                        ->columnSpan(1),    
                    ])
                    ->columns(4),
                ])->columns(1),
                Grid::make([
                    'default' => 1
                ])
                ->relationship('employment')
                ->schema([
                    Section::make('Shift Schedule')
                    ->description('')
                    ->icon('heroicon-s-clock')
                    ->schema([
                        TimePicker::make('time_in')
                        ->seconds(false)
                        ->label('Time-in'),
                        TimePicker::make('time_out')
                        ->seconds(false)
                        ->label('Time-out'),
                    ])->columns(),
                    Radio::make('overtime_entitlement')
                    ->label('Entitled for Overtime')
                    ->boolean()
                    ->inline()
                    ->inlineLabel(false),
                ])
                ->columns(1)
                ->grow(false),
            ])
            ->from('lg')
        ]);
    }

    public static function employementPositionInformation(): Section
    {
        return Section::make('POSITION DETAILS')
        ->description('Employee Position Information')
        ->icon('heroicon-s-flag')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->relationship('position')
            ->schema([
                Select::make('job_position')->label('Position')
                ->options([
                    'BACKEND' => 'Back-end Developer',
                    'FRONTEND' => 'Front-end Developer',
                    'FULLSTACK' => 'Full-stack Developer'
                ])
                ->required()
                ->searchable(),
                Select::make('job_category')
                ->options([
                    'SPECIALIST' => 'Specialist',
                ])
                ->required()
                ->searchable(),
                // Select::make('joined_designation'),
                Select::make('reporting_person')->label('Reporting To')
                ->required()
                ->options(User::all()->pluck('name','user_id')->map(function ($name) {
                    return ucwords(strtolower($name));
                })->toArray())
                ->searchable()
                ->preload(),
                Select::make('reporting_designation')->label('Reporting Designation/Department')
                ->options([
                    'ITDEPARTMENT' => 'IT Deparment',
                    'FINANCE' => 'Finance Department',
                    'HUMANRESOURCE' => 'Human Resource Department'
                ])
                ->preload()
                ->required()
                ->searchable(),
                Select::make('location')->label('Location/Office')->options([
                    'ILOILO' => 'Iloilo Main Office',
                    'BACOLOD' => 'Bacolod Main Office',
                    'BOHOL' => 'Bohol Main Office'
                ])
                ->required()
                ->searchable(),
                Textarea::make('job_description')->columnSpanFull()
            ])->columns(3),
        ]);
    }

    public static function employeeRequestApprover(): Section
    {
        return Section::make("EMPLOYEE REQUEST APPROVER'S")
        ->description('Request approvers')
        ->icon('heroicon-o-shield-check')
        ->schema([
            Repeater::make('employee_request_approvers')
            ->label('')
            ->relationship()
            ->simple(
                Select::make('approver_id')
                ->options(User::all()->pluck('name', 'user_id')->map(function ($name) {
                    return ucwords(strtolower($name));
                }))
                ->label('Approver')
                ->preload()
                ->required()
                // ->live()
                // ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {

                //         $family_id = $state;
                //         $family = EmployeeFamilyDetail::where('employee_family_id', $family_id)->first();
                //         $set('relationship', $family->relationship);
                // })
                ->searchable()
            )
            ->addActionLabel('Add Approver')
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation()
            )
        ])
        ;
    }

    public static function issuedItemInformation(): Section
    {
        return Section::make('ISSUED ITEM DETAILS')
        ->description('Employee Issued Item Information')
        ->icon('heroicon-s-wrench-screwdriver')
        ->schema([
            Repeater::make('issued_items')
            ->label('')
            ->relationship()
            ->schema([
                    Select::make('item_type')
                    ->options([
                        'MOBILE' => 'Mobile Phone',
                        'LAPTOP' => 'Laptop',
                        'VEHICLE' => 'Vehicle',
                        'MOTORCYCLE' => 'Motorcycle',
                    ])
                    ->required(),
                    TextInput::make('item_name'),
                    TextInput::make('item_model'),   
                    DatePicker::make('issued_date')
                    ->label('Issued Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),
            ])
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->itemLabel(function (array $state): ?string {
                if ($state['item_name']) {
                    return strtoupper($state['item_name']);
                } 
                return null;
            })
            ->columns(4)
        ])->collapsed(false);
    }

    public static function salaryInformation() : Section
    {
        return Section::make(function (EmployeeManagement $employee) {
            return 'SALARY DETAILS : '. strtoupper($employee->user->fullname) ?? 'SALARY DETAILS';
        })
        ->description()
        ->icon('heroicon-s-banknotes')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Placeholder::make('fullname')->label('Employee Name')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->user->fullname) ? $record->user->fullname : ""),
                Placeholder::make('category')->label('Category')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->position->job_category) ? $record->position->job_category : ""),
                Placeholder::make('job_position')->label('Job Position')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->position->job_position) ? $record->position->job_position : ""),
                Placeholder::make('payroll_cycle')->label('Payroll Cycle')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->employment->payroll_cycle) ? $record->employment->payroll_cycle : ""),
                Placeholder::make('payment_structure')->label('Payment Structure')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->employment->payment_structure) ? $record->employment->payment_structure : ""),
                Placeholder::make('employement_date')->label('Effective Date From')
                ->content(fn (EmployeeManagement $record): ?string => isset($record->employment->employement_date) ? $record->employment->employement_date : ""),
                Grid::make([
                    'default' => 1
                ])
            ])->columns(6),
        ]);
    }

    public static function payComponentInformation(): Section
    {
        return Section::make('Pay Component')
        ->schema([
            Tabs::make('Tabs')
            ->tabs([
               Tab::make('View')
                    ->schema([
                        static::viewSalaryRepeater(),
                    ]),
               Tab::make('Add/Modify')
                    ->schema([
                        static::getAddSalaryRepeater(),
                    ]),
               Tab::make('History')
                    ->schema([

                    ]),
            ])->contained(false),
        ]);     
    }

    public static function viewSalaryRepeater(): Grid
    {
        return 
        Grid::make([
            'default' => 1
        ])
        ->schema([
            Livewire::make(ViewSalaryDetails::class)->lazy(),    
        ]);
    }

    public static function getAddSalaryRepeater(): Grid
    {
        $salary = Grid::make([
            'default' => 1
        ])
        ->schema([
            // Add salary Header
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Placeholder::make('')->content('Guaranteed'),
                Placeholder::make('')->content('Type'),
                Placeholder::make('')->content('Amount'),
                Placeholder::make('')->content('Effective Date'),
                Placeholder::make('')->content('Expiration Date'),
                Placeholder::make('')->content('Is Taxable'),
                Placeholder::make('')->content('Pay Period'),
            ])->columns(7),

            // Add salary Body repeater
            // added this package composer require icetalker/filament-table-repeater to make the repeater not spacious
            Repeater::make('salary')
            ->label('')
            ->relationship()
            ->schema([
                Select::make('name')->label('Name')
                ->options([
                    'BASIC-SALARY' => 'Basic Salary',
                    'DE-MINIMIS' => 'De Minimis Allowance',
                    'MEDICAL' => 'Medical Allowance',
                    'CLOTHING' => 'Clothing Allowance',
                    'TRANSPORATION' => 'Transportation Allowance',
                    '13-MONTH' => '13th Month Pay',
                    '14-MONTH' => '14th Month Pay',
                ])
                ->preload()
                ->required()
                ->searchable(),
                Select::make('type')->label('Type')
                ->options([
                    'basic' => 'Basic',
                    'allowance' => 'Allowance',
                    'bonuses' => 'Bonuses',
                ])
                ->preload()
                ->required()
                ->searchable(),
                Select::make('pay_period')->label('Pay Period')
                ->options([
                    'weekly' => 'Weekly',
                    'bi-weekly' => 'Bi-weekly',
                    'monthly' => 'Monthly',
                    'annually' => 'Annually',
                ])
                ->preload()
                ->required()
                ->searchable(),
                TextInput::make('amount')
                ->prefix('₱')
                ->numeric()
                ->required()
                ->placeholder(0),
                DatePicker::make('effective_date'),
                DatePicker::make('expiration_date'),
                Checkbox::make('is_taxable')
            ])
            ->live()
            ->addActionLabel('Add Pay Component')
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->columns(7),
        ]);

        return $salary;
    }


    public static function parentInformartion() : Section 
    {
        return Section::make('Family Information')
        ->description('Employee Family Details')
        ->icon('heroicon-s-users')
        ->schema([
            Section::make("FATHER'S DETAILS")
            ->relationship('employeeFather')
            ->schema([
                static::parentFieldInformation()
            ]),

            Section::make("MOTHER'S DETAILS")
            ->relationship('employeeMother')
            ->schema([
                static::parentFieldInformation()
            ]),
        ])->collapsed(false);
    }

    public static function parentFieldInformation() : Split 
    {
        return Split::make([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                TextInput::make('name')->label('Full Name'),
                TextInput::make('occupation'),
                TextInput::make('employer'),
            ])->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                DatePicker::make('birthdate')->label('Date of Birth')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),
                TextInput::make('mobile')
                ->suffixIcon('heroicon-o-device-phone-mobile'),
                TextInput::make('address')
                ->suffixIcon('heroicon-o-map'),
            ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Placeholder::make('age')->label("Age")
                ->content(function ($record) {
                    return static::getAge(isset($record->birthdate) ? $record->birthdate : "");
                })
            ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Toggle::make('is_alive')
                ->label('is Alive?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_medical_entitled')
                ->label('is Covered by medical scheme?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_disabled')
                ->label('is Disabled')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                // Toggle::make('is_dependent')
                // ->label('is Dependent? (Field disabled*)')
                // ->onIcon('heroicon-s-check')
                // ->offIcon('heroicon-s-x-mark')
                // ->inline()
                // ->disabled()                                                        
            ])
            ->columns(1)
        ])->from('lg');
    }

    public static function spouseInformation() : Section 
    {
        return Section::make('Spouse Information')
        ->description('Employee Spouse Details')
        ->icon('heroicon-s-user')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->relationship('employeeSpouse')
            ->schema([
                static::spouseInformationFields()
            ])->columns(1),
        ])->collapsed(false);
    }

    public static function spouseInformationFields() : Split 
    {
        return Split::make([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                TextInput::make('name')->label('Full Name'),
                TextInput::make('occupation'),
                TextInput::make('employer'),
            ])->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                DatePicker::make('birthdate')->label('Date of Birth')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),
                TextInput::make('mobile')
                ->suffixIcon('heroicon-o-device-phone-mobile'),
                TextInput::make('address')
                ->suffixIcon('heroicon-o-map'),
            ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Placeholder::make('age')->label("Age")
                ->content(function ($record) {
                        return static::getAge(isset($record->birthdate) ? $record->birthdate : "");
                    }),
                    DatePicker::make('anniversary')->label('Anniversary Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),
                    TextInput::make('relationship')
                    ->default('SPOUSE')
                    ->readOnly()
                ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Toggle::make('is_alive')
                ->label('is Alive?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_medical_entitled')
                ->label('is Covered by medical scheme?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_disabled')
                ->label('is Disabled')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                // Toggle::make('is_dependent')
                // ->label('is Dependent? (Field disabled*)')
                // ->onIcon('heroicon-s-check')
                // ->offIcon('heroicon-s-x-mark')
                // ->inline()
                // ->disabled()                                                        
            ])
            ->columns(1)
        ])->from('lg');
    }

    public static function childrenInformation() : Section 
    {
        return Section::make("CHILDREN'S INFORMATION")
        ->icon('heroicon-s-user-group')
        ->description("Employee Children's Details")
        ->schema([
            Repeater::make('employeeChildren')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    static::childerInformationFields(),
                ])->columns(1)
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['name']) {
                    return strtoupper($state['name']);
                } 
                return null;
            })
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            )
            ->reorderable(false)
            ->columns(1)
        ])
        ->collapsed(false)
        ->columns(1);   
    }

    public static function childerInformationFields() : Split 
    {
        return Split::make([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                TextInput::make('name')->label('Full Name'),
                TextInput::make('occupation'),
                TextInput::make('employer'),
            ])->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                DatePicker::make('birthdate')->label('Date of Birth')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),
                TextInput::make('mobile')
                ->suffixIcon('heroicon-o-device-phone-mobile'),
                TextInput::make('address')
                ->suffixIcon('heroicon-o-map'),
            ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Placeholder::make('age')->label("Age")
                ->content(function ($record) {
                         return static::getAge(isset($record->birthdate) ? $record->birthdate : "");
                    }),
                    TextInput::make('school')->label('School/Institute'),
                    TextInput::make('relationship')
                    ->default('CHILD')
                    ->readOnly()
                ])
            ->columns(1),
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Toggle::make('is_alive')
                ->label('is Alive?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->default(1)
                ->inline(),
                Toggle::make('is_medical_entitled')
                ->label('is Covered by medical scheme?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_disabled')
                ->label('is Disabled?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),
                Toggle::make('is_adopted')
                ->label('is Adopted?')
                ->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->inline(),              
                // Toggle::make('is_dependent')
                // ->label('is Dependent? (Field disabled*)')
                // ->onIcon('heroicon-s-check')
                // ->offIcon('heroicon-s-x-mark')
                // ->inline()
                // ->disabled()                                           
            ])
            ->columns(1)
        ])->from('lg');
    }

    public static function getAge($birthdate): ?string
    {
        if ($birthdate) {
            $birthdate = Carbon::parse($birthdate);
            $ageYears = $birthdate->age;
            $ageMonths = $birthdate->diff(Carbon::now())->format('%m');
    
            if ($ageMonths > 0) {
                return "$ageYears years and $ageMonths months old";
            } else {
                return "$ageYears years old";
            }
        } else {
            return "N/A";
        }
    }

    public static function educationAndWorkHistoryInformation() : Grid
    {
        return Grid::make([
            'default' => 1
        ])
        ->schema([
            static::educationFields(),
            static::employmentHistory()
        ]);
    }

    public static function educationFields(): Section
    {
        return Section::make('EDUCATION DETAILS')
        ->description('Employee Education Information')
        ->icon('heroicon-m-academic-cap')
        ->schema([
            Repeater::make('education')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('school')->label('School/Institute'),
                    Select::make('course')->options([
                        'B.S in Information Technology' => 'B.S in Information Technology',
                        'B.S in Civil Engineering' => 'B.S in Civil Engineering'
                    ]),
                    Select::make('degree')->options([
                        'ELEMENTARY' => 'Elementary',
                        'HIGHSCHOOL' => 'HighSchool',
                        'COLLEGE' => 'College',
                        'MASTERAL' => 'Masteral',
                        'EDUCATION' => 'Education'
                    ]),
                ])
                ->columns(3),    
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    DatePicker::make('year_started')
                    ->label('Year Start Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),
                    DatePicker::make('year_end')
                    ->label('Year End Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),   
                ])
                ->columns(2),                  
                TextArea::make('remarks')
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['school']) {
                    return strtoupper($state['school']);
                } 
                return null;
            })->collapsed()
            ->columns(1)
        ]);
    }

    public static function employmentHistory(): Section
    {
        return Section::make('EMPLOYMENT HISTORY DETAILS')
        ->description('Employee Employment History Information')
        ->icon('heroicon-s-newspaper')
        ->schema([
            Repeater::make('employment_history')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('company_name')->label('Company Name'),
                    TextInput::make('job_title')->label('Job Title'),
                    TextInput::make('job_description')->label('Job Description'),
                ])
                ->columns(3),    
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    DatePicker::make('start_date')
                    ->label('Start Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),
                    DatePicker::make('end_date')
                    ->label('End Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),   
                ])
                ->columns(2)                 
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['company_name']) {
                    return strtoupper($state['company_name']);
                } 
                return null;
            })->collapsed()
            ->columns(1)
        ]);
    }

    public static function dependentAndhealthBenefitInformation($model_record) : Split
    {
        return Split::make([
            static::healthBenefitFields(),
            static::dpendentFields($model_record)
        ])->from('lg');
    }

    public static function healthBenefitFields(): Section
    {
        return Section::make('HEALTH BENEFIT DETAILS')
        ->description('Employee Health Benefit Information')
        ->icon('heroicon-c-plus-circle')
        ->schema([
            Repeater::make('healthBenefits')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('name'),
                    DatePicker::make('enrollment_date')
                    ->label('Enrollment Date')
                    ->suffixIcon('heroicon-o-calendar-days')
                    ->maxDate(now()),
                    TextInput::make('monthly_premium')   
                ])
                ->columns(3),    
                DatePicker::make('coverage_start_date')
                ->label('Coverage Start Date')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),
                DatePicker::make('coverage_end_date')
                ->label('Coverage End Date')
                ->suffixIcon('heroicon-o-calendar-days')
                ->maxDate(now()),   
            ])
            ->itemLabel(function (array $state): ?string {
                if ($state['name']) {
                    return strtoupper($state['name']);
                } 
                return null;
            })
            ->columns(2)
        ]);
    }

    public static function dpendentFields($model_record): Section
    {
        return   Section::make('DEPENDENT DETAILS')
        ->description('Employee Dependent Information')
        ->icon('heroicon-c-face-smile')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Repeater::make('dependents')
                ->label('')
                ->relationship()
                ->simple(
                    Select::make('employee_family_id')
                    ->options(function () use ($model_record) {

                        if (!$model_record) {
                            return [];
                        }
                
                        return EmployeeFamilyDetail::where('employee_id', $model_record->employee_id)
                            ->pluck('name', 'employee_family_id')
                            ->map(function ($name) {
                                return ucwords(strtolower($name));
                            })
                            ->toArray();
                    })
                    ->label('Dependent Name')
                    ->preload()
                    ->required()
                    ->live()
                    // ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {

                    //         $family_id = $state;
                    //         $family = EmployeeFamilyDetail::where('employee_family_id', $family_id)->first();
                    //         $set('relationship', $family->relationship);
                    // })
                    ->searchable()
                )
                ->addActionLabel('Add Dependent')
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation()
                )
                // ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                //     // if UPDATE existing Dependent family
                //     if(isset($data['employee_family_id'])){
                //         $family = EmployeeFamilyDetail::where('employee_family_id', $data['employee_family_id'])->first();
             
                //         if ($family) {
                //             $family->is_dependent = true; 
                //             $family->save();
                //         }
                //     }

                //     return $data;
                // })
                ->columns(1), 
            ])
            ->columns(1)
        ]);
    }    

    public static function trainingInformation() : Section
    {
         return Section::make('TRAINING DETAILS')
        ->description('Employee Training Information')
        ->icon('heroicon-s-arrow-trending-up')
        ->schema([
            Repeater::make('trainings')
            ->label('')
            ->relationship()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Select::make('training_status_type_id')->options([
                        '1' => 'Ongoing',
                        '2' => 'Completed'
                    ])
                    ->required()
                    ->preload(),
                    Select::make('training_type_id')->options([
                        '1' => 'Workshop',
                        '2' => 'Online Course',
                        '3' => 'Certification Program',
                    ])
                    ->required()
                    ->preload(),
                    TextInput::make('course_title'),
                    TextInput::make('course_url'),
                    TextArea::make('description'),
                    TextInput::make('credit_hours'),
                    DatePicker::make('start_date')
                    ->suffixIcon('heroicon-o-calendar-days'),
                    DatePicker::make('completion_date')
                    ->suffixIcon('heroicon-o-calendar-days')
                ])->columns(4),
            ])
            ->deleteAction(
                fn (Action $action) => $action->requiresConfirmation(),
            ),
        ])->collapsed(false);
    }

    public static function idInformation(): Section
    {
        return Section::make(function (EmployeeManagement $employee) {
            return 'ID DETAILS - EMPLOYEE NUMBER : '. strtoupper($employee->employee_reference) ?? 'ID DETAILS';
        })
        ->description('Employee ID Information')
        ->icon('heroicon-s-identification')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->relationship('id_details')
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('sss_number')->label('SSS ID :'),
                    TextInput::make('pagibig_number')->label('PAG-IBIG ID :'),
                    TextInput::make('philhealth_number')->label('PHILHEALTH ID :'),
                    TextInput::make('tin_number')->label('TIN ID :'),
                ])->columns(4)
            ]),
        ]);
    }

    public static function bankInformation(): Section
    {
        return Section::make(['BANK DETAILS'])
        ->description('Employee Bank Information')
        ->icon('heroicon-s-credit-card')
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->relationship('bank')
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    TextInput::make('bank_name')->label('Bank Name'),
                    TextInput::make('account_name')->label('Account Name'),
                    TextInput::make('account_no')->label('Account Number'),
                ])->columns(3)
            ]),
        ]);
    }

    public static function documentInformation(): Section
    {
        return Section::make('DOCUMENTS')
        ->description('Employee Document Information List')
        ->icon('heroicon-s-clipboard-document')
        ->headerActions([
            // Action::make('Upload')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->form([
            //         Section::make('')->label('')
            //         ->schema([
            //             Grid::make([
            //                 'default' => 1
            //             ])
            //             ->schema([
            //                 Select::make('document_type')->label("Document Type")->options([
            //                     'REQUIREMENTS' => 'Requirements',
            //                     'PHILHEALTH' => 'Phil-Health ID',
            //                     'PAGIBIG' => 'Pag-ibig ID',
            //                     'SSS' => 'SSS ID',
            //                     'TIN' => 'TIN ID',
            //                     'TOR' => 'Transcript of Records',
            //                     'OTHERS' => "Other's"
            //                 ])
            //                 ->searchable(),
            //                TextArea::make('document_remarks')->label('Document Remarks'),
            //             ])->columns(2),
            //             FileUpload::make('attachments')
            //             ->disk('public')
            //             ->directory('document/attachments')
            //             ->multiple()
            //         ])
            //     ])
            //     ->action(function (array $data,$record) {

            //        $result = EmployeeDocument::create([
            //             'employee_id' => $record->employee_id,
            //             'document_type' => $data['document_type'],
            //             'document_remarks' => $data['document_remarks'],
            //         ]);

            //         foreach($data['attachments'] as $attachment){

            //             $file = Storage::disk('public')->exists($attachment);

            //             if($file) {
            //                 $path = $attachment;
            //                 $filename = pathinfo($path, PATHINFO_FILENAME);
            //                 $type = pathinfo($path, PATHINFO_EXTENSION);
            //                 $result->attachments()->create([
            //                     'filename'=> $filename,
            //                     'type' => $type,
            //                     'path' => $path
            //                 ]);

                            
            //                 Notification::make()
            //                 ->title('Upload file successfully.')
            //                 ->success()
            //                 ->send();
            //             }
            //         }

            //         redirect()->route('filament.admin.resources.employees.edit', ['record' => $record->employee_id, 'tab' => '-document-details-tab']);
            //     }),
        ])
        ->schema([
            Grid::make([
                'default' => 1
            ])
            ->schema([
                Repeater::make('employeeDocuments')
                ->label('')
                ->relationship()
                ->schema([
                        Grid::make([
                                'default' => 1
                        ])
                        ->schema([
                            Select::make('document_type')->label("Document Type")->options([
                                'REQUIREMENTS' => 'Requirements',
                                'PHILHEALTH' => 'Phil-Health ID',
                                'PAGIBIG' => 'Pag-ibig ID',
                                'SSS' => 'SSS ID',
                                'TIN' => 'TIN ID',
                                'TOR' => 'Transcript of Records',
                                'OTHERS' => "Other's"
                            ])
                            ->required()
                            ->searchable(),
                            TextInput::make('document_remarks')
                            ->label('Document Remarks'),
                        ])->columns(2),
                        Grid::make([
                            'default' => 1
                        ])
                        ->schema([      
                            Repeater::make('attachments')
                            ->label('')
                            ->relationship()
                            ->simple(
                                // TextInput::make('filename')
                                // ->readOnly(),
                                FileUpload::make('path')
                                ->panelAspectRatio('2:1')
                                ->label('')
                                ->disk('public')
                                ->directory('document/attachments')  
                                ->storeFileNamesIn('filename')                          
                                ->previewable()
                                ->openable()
                                ->downloadable()
                            )
                            ->grid(3)
                            ->addActionLabel('New Attachment')   
                        ])       
                ])
                ->collapsed()
                ->addActionLabel('Add Document')   
                ->itemLabel(function (array $state): ?string {
                    if ($state['document_type']) {
                        return strtoupper($state['document_type']);
                    } 
                    return null;
                })
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )
            ]),
        ]);
    }
}
