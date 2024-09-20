<?php

namespace App\Filament\Resources\MyPayslipResource\Pages;

use App\Filament\Resources\MyPayslipResource;
use App\Models\Payroll;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Livewire\Component;
use PhpParser\Node\Stmt\Label;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class MyPayslipViewTable extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use HasTabs;

    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    protected ?String $heading = '';
        
    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    protected function getTableQuery(): QueryBuilder
    {
        $employee_id = $this->record->employee_id;

        return Payroll::query()->where('employee_id', $employee_id);
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->modifyQueryUsing(function ($query) {
                $query;
            }),
            'January' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 1);
            }),
            'February' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 2);
            }),
            'March' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 3);
            }),
            'April' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 4);
            }),
            'May' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 5);
            }),
            'June' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 6);
            }),
            'July' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 7);
            }),
            'August' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 8);
            }),
            'September' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 9);
            }),
            'October' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 10);
            }),
            'November' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 11);
            }),
            'December' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('created_at', 12);
            }),
        ];
    }

    
    public function getDefaultActiveTab(): string | int | null
    {
        // not working - will have query of the current month here
        return 'June';
    }

    protected function modifyQueryWithActiveTab(QueryBuilder $query): QueryBuilder
    {
        if (blank(filled($this->activeTab))) {
            return $query;
        }

        if(empty($this->activeTab)){
            $this->activeTab = now()->format('F');  // Set the current month as the active tab
        }

        $tabs = $this->getCachedTabs();

        if (! array_key_exists($this->activeTab, $tabs)) {
            return $query;
        }

        return $tabs[$this->activeTab]->modifyQuery($query);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->columns([
                TextColumn::make('fullname')->label('Employee Name')->searchable(),
                TextColumn::make('cut_off_from')->label('Pay Period From'),
                TextColumn::make('cut_off_to')->label('Pay Period To'),
                TextColumn::make('cut_off'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginated([31, 'all'])
            ->filters([
                SelectFilter::make('year')
                ->label('Year')
                ->options($this->generateYearOptions())
                ->default(date('Y'))
                ->query(function ($query, array $data) {

                    if (isset($data['value'])) {
                        $query->whereYear('created_at', $data['value']);
                    }

                    $query->where('employee_id', $this->record->employee_id);
                }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            // ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('pdf')
                ->label('Download Payslip')
                ->color('danger')
                ->icon('heroicon-s-document-arrow-down')
                ->action(function (Payroll $record, array $data) {

                    redirect()->route('download.payslip.pdf', ['payroll_id' => $record->payroll_id]);
                }),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    // I dont understand how this work yet as a whole - this is in regards for the tabs in timesheet component to work
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

    protected function generateYearOptions(): array
    {
        $currentYear = date('Y');
        $years = range(2000, $currentYear);
        
        // Format the array for the select filter options
        return array_combine($years, $years);
    }

    protected static string $view = 'filament.resources.my-payslip-resource.pages.my-payslip-view-table';
}
