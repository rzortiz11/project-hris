<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Filament\Resources\LeaveResource\Widgets\LeaveAllocationPieChart;
use App\Livewire\EmployeeLeaveHistoryTable;
use App\Livewire\ViewSalaryDetails;
use App\Models\Employee;
use App\Models\EmployeeManagement;
use App\Models\Leave;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class LeaveResource extends Resource
{
    protected static ?string $model = EmployeeManagement::class;

    protected static ?string $navigationIcon = 'heroicon-s-calendar';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Leave Management';

    protected static ?string $modelLabel = "Employee Leaves";

    public static function form(Form $form): Form
    {
        // Access the model
        $model_record = $form->getRecord();

        return $form
            ->schema([
                Section::make('Employee Leave Details')
                ->description('Leave Information')
                ->icon('heroicon-m-arrow-right-start-on-rectangle')
                ->schema([
                    Placeholder::make('Employee Number')
                    ->content(fn (Employee $record): ?string => $record ? $record->employee_reference : ""),
                    Placeholder::make('Employee Name')
                    ->content(fn (Employee $record): ?string => $record ? $record->user->name : ""),
                    Placeholder::make('Category')
                    ->content(fn (Employee $record): ?string => isset($record->position) ? $record->position->job_category : "N/A"),
                    Placeholder::make('Position')
                    ->content(fn (Employee $record): ?string => isset($record->position) ? $record->position->job_position : "N/A"),
                    Placeholder::make('Department')
                    ->content(fn (Employee $record): ?string => isset($record->position) ? $record->position->reporting_designation : "N/A"),
                ])->columns(5),

                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Split::make([
                        Section::make('EMPLOYE LEAVE DETAILS')
                        ->description('LEAVE ALLOCATIONS')
                        ->icon('heroicon-o-document-duplicate')
                        ->schema([
                            Repeater::make('employee_leave_balances')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Grid::make([
                                    'default' => 1
                                ])
                                ->schema([
                                    Select::make('type')->options([
                                        'Vaction Leave' => 'Vacation Leave',
                                        'Sick Leave' => 'Sick Leave',
                                        'Maternity Leave' => 'Maternity Leave',
                                        'Leave Without Pay' => 'Leave Without Pay',
                                    ])
                                    ->required()
                                    ->preload(),
                                    TextInput::make('balance') 
                                    ->required(),
                                    Radio::make('is_paid')
                                    ->label('')
                                    ->options([
                                        '1' => 'Is paid',
                                        '0' => 'Is not paid',
                                    ])
                                    ->descriptions([
                                        '1' => 'Leave with pay',
                                        '0' => 'Leave with out pay',
                                    ])
                                ])
                                ->columns(3),    
                            ])
                            ->itemLabel(function (array $state): ?string {
                                if ($state['type']) {
                                    return strtoupper($state['type']) . ' - ' . 'Balance : '.$state['balance'];
                                } 
                                return null;
                            })->collapsed()
                        ]),
                        Section::make('ALLOCATION DETAILS')
                        ->description('LEAVE ALLOCATIONS')
                        ->icon('heroicon-o-chart-pie')
                        ->schema([
                            Livewire::make(LeaveAllocationPieChart::class)
                            ->data(['record' => $model_record])
                            ->key(self::generateUuid())
                            ->lazy(),
                        ]),
                        Section::make("EMPLOYEE LEAVE APPROVER'S")
                        ->description('LEAVE APPROVERS')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Repeater::make('employee_leave_approvers')
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
                        ]),
                    ])
                    ->from('lg'),
                    Section::make('EMPLOYEE LEAVE HISTORY')
                    ->icon('heroicon-s-document-duplicate')
                    ->schema([
                        Livewire::make(EmployeeLeaveHistoryTable::class)->key(self::generateUuid())->lazy() // do not remove the lazy this will cause for the button to not load
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')->label('ID'),
                ImageColumn::make('avatar')
                ->grow(false)
                ->getStateUsing(function (Employee $data): string {

                    return isset($data->picture) ? $data->picture : '';
                })
                ->circular(),
                TextColumn::make('employee_reference')->searchable(),
                TextColumn::make('user.name')->label('User')->searchable(['first_name','last_name']),
                TextColumn::make('user.name')->label('User')->searchable(['first_name','last_name']),
                TextColumn::make('position.job_position')->label('Position'),
                TextColumn::make('position.reporting_designation')->label('Designation'),
                TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (Employee $record): string {
                    return $record->is_active ? 'active': 'inactive';
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
           
            ]);
            // ->poll('10s');
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
