<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Payroll;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Colors\Color;

class ViewEmployeePayrollTable extends Page implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;
    use HasTabs;

    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    protected function getTableQuery(): QueryBuilder
    {
        $pay_period_id = $this->record->pay_period_id;
        return Payroll::query()->where('pay_period_id', $pay_period_id);
    }
    
    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->icon('heroicon-s-document-text')
            ->badge(function () {
                return Payroll::query()->where('pay_period_id', $this->record->pay_period_id)
                ->count();
            }),
            'Pending' => Tab::make()->modifyQueryUsing(function ($query){
                $query->where('status',config('constants.PAYROLL_PENDING'));
            })
            ->badge(function () {
                return Payroll::query()->where('pay_period_id', $this->record->pay_period_id)->where('status',config('constants.PAYROLL_PENDING'))
                ->count();
            })
            ->badgeColor('warning'),
            'Approved' => Tab::make()->modifyQueryUsing(function ($query){
                $query->where('status',config('constants.PAYROLL_APPROVED'));
            })
            ->badge(function () {
                return Payroll::query()->where('pay_period_id', $this->record->pay_period_id)->where('status',config('constants.PAYROLL_APPROVED'))
                ->count();
            })
            ->badgeColor('success'),
            'Denied' => Tab::make()->modifyQueryUsing(function ($query){
                $query->where('status',config('constants.PAYROLL_DENIED'));
            })
            ->badge(function () {
                return Payroll::query()->where('pay_period_id', $this->record->pay_period_id)->where('status',config('constants.PAYROLL_DENIED'))
                ->count();
            })
            ->badgeColor('danger'),
            'Void' => Tab::make()->modifyQueryUsing(function ($query){
                $query->where('status',config('constants.PAYROLL_VOID'));
            })
            ->badge(function () {
                return Payroll::query()->where('pay_period_id', $this->record->pay_period_id)->where('status',config('constants.PAYROLL_VOID'))
                ->count();
            })
            ->badgeColor(Color::Gray),
        ];
    }

    protected function modifyQueryWithActiveTab(QueryBuilder $query): QueryBuilder
    {
        if (blank(filled($this->activeTab))) {
            return $query;
        }

        if(empty($this->activeTab)){
            // $this->activeTab = now()->format('F');  // Set the current month as the active tab
            $this->activeTab = 'All';  // Set the current month as the active tab
        }

        $tabs = $this->getCachedTabs();

        if (! array_key_exists($this->activeTab, $tabs)) {
            return $query;
        }

        return $tabs[$this->activeTab]->modifyQuery($query);
    }

       // https://github.com/filamentphp/filament/discussions/10127 - link of the discussion
    protected function makeTable(): Table
    {
        return $this->makeBaseTable()
            ->query(fn (): QueryBuilder => $this->getTableQuery())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            //->modelLabel($this->getModelLabel() ?? static::getResource()::getModelLabel())
            //->pluralModelLabel($this->getPluralModelLabel() ?? static::getResource()::getPluralModelLabel())
            ->recordAction(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);
    
                    if (! $action) {
                        continue;
                    }
    
                    $action->record($record);
    
                    if ($action->isHidden()) {
                        continue;
                    }
    
                    if ($action->getUrl()) {
                        continue;
                    }
    
                    return $action->getName();
                }
    
                return null;
            })
            //->recordTitle(fn (Model $record): string => static::getResource()::getRecordTitle($record))
            ->recordUrl($this->getTableRecordUrlUsing() ?? function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);
    
                    if (! $action) {
                        continue;
                    }
    
                    $action->record($record);
    
                    if ($action->isHidden()) {
                        continue;
                    }
    
                    $url = $action->getUrl();
    
                    if (! $url) {
                        continue;
                    }
    
                    return $url;
                }
                return null;
            });
    
    }

    public function table(Table $table): Table
    {
        $pay_period_id = $this->record->pay_period_id;
        
        return $table
            ->query(Payroll::query()->where('pay_period_id', $pay_period_id))
            // ->defaultGroup('status')
            ->columns([
                TextColumn::make('status')->label('Status')
                ->badge()
                ->color(fn (string $state): string => match($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'denied' => 'danger',
                    'void' => 'danger',
                }),
                ColumnGroup::make('Employee Details', [
                    TextColumn::make('payroll_id')->label('ID'),
                    ImageColumn::make('avatar')
                    ->grow(false)
                    ->getStateUsing(function ($record): string {
                        $employee = Employee::find($record->employee_id);
                        return isset($employee->picture) ? $employee->picture : '';
                    })
                    ->circular(),
                    TextColumn::make('fullname')->label('Employee Name')->searchable(),
                    TextColumn::make('job_position')->label('Position'),
                    TextColumn::make('reporting_designation')->label('Designation'),
                    TextColumn::make('location')->label('Location'),
                    TextColumn::make('company')->label('Company'),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),

                // TextColumn::make('total_gross_pay')->label('Gross Pay'),
                TextColumn::make('basic_pay')->label('Basic Pay/cutoff')->prefix('₱')->alignEnd(),
                TextColumn::make('total_gross_pay')->label('Total Gross Pay')->prefix('₱')->weight(FontWeight::Bold)->alignEnd()
                ->extraAttributes(function ($state) {
                    $bgColor = '#d3d3d3';
                    return ['style' => "background-color: {$bgColor}"];
                }),
                ColumnGroup::make('Mandatory Contributions', [
                    TextColumn::make('sss_contribution')->label('SSS')->alignEnd(),
                    TextColumn::make('pagibig_contribution')->label('Pag-Ibig')->alignEnd(),
                    TextColumn::make('philhealth_contribution')->label('PhilHealth')->alignEnd(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('other_deductions')->label('Other Deductions')->alignEnd(),
                ColumnGroup::make('Income', [
                    TextColumn::make('taxable_income')->label('Taxable')->alignEnd(),
                    TextColumn::make('income_tax_withheld')->label('Tax with Held')->alignEnd(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('cash_advance')->label('Cash Advance')->alignEnd(),
                TextColumn::make('adjustment')->label('Adjsutment')->alignEnd(),
                TextColumn::make('total_net_pay')->label('Total Net Pay')->prefix('₱')->weight(FontWeight::Bold)->alignEnd()
                ->summarize(Sum::make()->label('Total')->numeric()->money('PHP'))
                ->extraAttributes(function ($state) {
                    $bgColor = '#d3d3d3';
                    return ['style' => "background-color: {$bgColor}"];
                }),
                CheckboxColumn::make('is_viewable')->label('Viewable'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('Approve')
                // ->color('success')
                // ->icon('heroicon-o-archive-box-x-mark')
                // ->action(function (Payroll $record, array $data) {
                //     $record->status = "approved";
                //     $record->save();
                // })->requiresConfirmation(),
                Tables\Actions\Action::make('pdf')
                ->label('Payslip')
                ->color('danger')
                ->icon('heroicon-s-document-arrow-down')
                ->action(function (Payroll $record, array $data) {

                    redirect()->route('download.payslip.pdf', ['payroll_id' => $record->payroll_id]);
                }),
                Tables\Actions\Action::make('approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action(function (Payroll $record, array $data) {
                    self::handleAudit($record, config('constants.PAYROLL_APPROVED'));
                }),
                Tables\Actions\ViewAction::make()->infolist(fn(Infolist $infolist) => ViewEmployeePayrollTable::infolist($infolist))->modalWidth('8xl'),
                // Tables\Actions\EditAction::make(), payslip should not be editable either create or delete only
                Tables\Actions\DeleteAction::make(),
         
            ])
            ->striped()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Approved')
                    ->color('success')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->requiresConfirmation()
                    ->action(function (Collection $records){
                        foreach ($records as $record) {
                            dump($record);
                        }
                        dump($records);
                    }),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
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
                                TextEntry::make('cut_off')
                                ->date()
                            ]),
                            Group::make([
                                TextEntry::make('cut_off_from')
                                 ->date(),
                                TextEntry::make('cut_off_to')
                                 ->date()
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
                                TextEntry::make('fullname'),
                                TextEntry::make('company')
                            ])->columns(3),
                            Group::make([
                                TextEntry::make('job_position'),
                                TextEntry::make('reporting_designation'),
                                TextEntry::make('location')
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
                                TextEntry::make('day_range'),
                                TextEntry::make('working_days'),
                                TextEntry::make('absent'),
                                TextEntry::make('time_change_hours'),
                                TextEntry::make('under_time_hours'),
                                TextEntry::make('retro_hours')
                        ])->columns(3),
                    ])->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Additional Work Hours')
                    ->collapsible()
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->schema([
                        Split::make([
                            TextEntry::make('regular_overtime_hours'),
                            Fieldset::make('Rest Day')
                            ->schema([
                                TextEntry::make('rest_day_hours')->label('Hours')->inlineLabel(),
                                TextEntry::make('rest_day_overtime_hours')->label('Over Time')->inlineLabel(),
                            ])->columns(1),
                            Fieldset::make('Legal Holiday')
                            ->schema([
                                TextEntry::make('legal_holiday_hours')->label('Hours')->inlineLabel(),
                                TextEntry::make('legal_holiday_overtime_hours')->label('Over Time')->inlineLabel(),
                            ])->columns(1),
                            Fieldset::make('Special Holiday')
                            ->schema([
                                TextEntry::make('special_holiday_hours')->label('Hours')->inlineLabel(),
                                TextEntry::make('special_holiday_overtime_hours')->label('Over Time')->inlineLabel(),
                            ])->columns(1),
                        ])
                    ])->columns(1),
                    Section::make('')
                    ->schema([
                        Split::make([
                            Fieldset::make('Leave With Pay')    
                            ->schema([
                                TextEntry::make('leave')->label('Days'),
                                TextEntry::make('leave_hours')->label('Hours'),
                            ]),
                            Fieldset::make('Late')
                            ->schema([
                                TextEntry::make('late_days')->label('Days'),
                                TextEntry::make('late_hours')->label('Hours'),
                            ]), 
                        ]),
                    ])->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Allowances')
                    ->schema([
                        Group::make([
                            RepeatableEntry::make('allowance')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('amount'),
                                TextEntry::make('is_taxable'),
                            ])->columns(3)
                        ])->columnSpanFull()
                    ])
                    ->collapsible()
                    ->columns(1)
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Deductions')
                    ->schema([
                        Group::make([
                            RepeatableEntry::make('deduction')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('amount'),
                            ])->columns(2)
                        ])->columnSpanFull()
                    ])->columns(1)
                    ->collapsible()
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);']),
                    Section::make('Bonuses')
                    ->schema([
                        Group::make([
                            RepeatableEntry::make('bonuses')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('amount'),
                                TextEntry::make('is_taxable'),
                            ])->columns(3)
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
                              
                            ])
                            ->grow(false)
                            ->columns(1),
                            Grid::make()
                            ->schema([
                              
                            ]),
                            Grid::make()
                            ->schema([
                                
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
                        TextEntry::make('basic_pay')->label('Basic Salary')->inlineLabel(),// update this to employee.salaray_details->basic_pay
                        TextEntry::make('basic_pay')->inlineLabel()->label('Salary Per Cutoff'),
                        TextEntry::make('leave_pay')->inlineLabel(), // check if i need to add this or not
                        TextEntry::make('over_time_pay')->inlineLabel(),
                        TextEntry::make('holiday_pay')->inlineLabel(),
                        TextEntry::make('retro_pay')->inlineLabel(),
                        TextEntry::make('allowances_pay')->inlineLabel(),
                        TextEntry::make('bonuses_pay')->inlineLabel(),
                        TextEntry::make('total_gross_pay')->inlineLabel(),
                        TextEntry::make('late_deductions')->inlineLabel(),// check if i need to add this or not
                        TextEntry::make('other_deductions')->inlineLabel(),
                        TextEntry::make('taxable_income')->inlineLabel(),
                        TextEntry::make('income_tax_withheld')->inlineLabel(),
                        TextEntry::make('cash_advance')->inlineLabel(),
                        TextEntry::make('adjustment')->inlineLabel(),
                        TextEntry::make('total_net_pay')->inlineLabel(),
                    ])->columns(1),
                    Section::make('')
                    ->extraAttributes(['style' => ' box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);'])
                    ->schema([
                        TextEntry::make('remarks')->inlineLabel(),

                        Actions::make([
                            Action::make('approve')
                                ->icon('heroicon-m-check')
                                ->color('success')
                                ->action(function (Payroll $record, array $data) {
                                    self::handleAudit($record, config('constants.PAYROLL_APPROVED'));
                                }),
                            Action::make('denied')
                                ->icon('heroicon-m-x-mark')
                                ->color('danger')
                                ->action(function (Payroll $record, array $data) {
                                    self::handleAudit($record, config('constants.PAYROLL_DENIED'));
                                }),
                            Action::make('void')
                                ->icon('heroicon-m-x-mark')
                                ->color(Color::Gray)
                                ->action(function (Payroll $record, array $data) {
                                    self::handleAudit($record, config('constants.PAYROLL_VOID'));
                                }),
                        ]),
                    ])->columns(1)
                ])  
                ->columnSpan(1),    
            ])
            ->columns(3),
        ]);
    }

    public function handleAudit(Payroll $record, $status)
    {
        // Set the status and audit flag
        $record->status = $status;
        $record->save();

        $notificationColor = $status === config('constants.PAYROLL_DENIED') || $status === config('constants.PAYROLL_VOID') 
            ? 'danger' 
            : 'success';

        Notification::make()
            ->title('Payroll')
            ->body(ucwords($status) . ' Successfully.')
            ->color($notificationColor)
            ->send();
    }

    // public function deleteAction(): Action
    // {
    //     return Action::make('delete')
    //         ->requiresConfirmation()
    //         ->action(fn () => $this->post->delete());
    // }

    public function render(): View
    {
        return view('livewire.view-employee-payroll-table');
    }
}
