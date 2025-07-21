<?php

namespace App\Livewire;

use App\Models\TimeSheet;
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
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class EmployeeTimeSheet extends Page implements HasForms, HasTable
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

        return TimeSheet::query()->where('employee_id', $employee_id);
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->modifyQueryUsing(function ($query) {
                $query;
            }),
            'January' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 1);
            }),
            'February' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 2);
            }),
            'March' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 3);
            }),
            'April' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 4);
            }),
            'May' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 5);
            }),
            'June' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 6);
            }),
            'July' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 7);
            }),
            'August' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 8);
            }),
            'September' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 9);
            }),
            'October' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 10);
            }),
            'November' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 11);
            }),
            'December' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereMonth('date', 12);
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
                TextColumn::make('date_type')
                ->label('')
                ->default('•')
                ->color(function (TimeSheet $timesheet): string {
                    $date = Carbon::parse($timesheet->date);
                    $dayOfWeek = $date->format('l');
            
                    // Check if the day is a weekend
                    $isWeekend = in_array($dayOfWeek, ['Saturday', 'Sunday']);
            
                    return $isWeekend ? 'danger' : 'success';
                })
                ->tooltip(function (TimeSheet $timesheet): string{
                    $date = Carbon::parse($timesheet->date);
                    $dayOfWeek = $date->format('l');
            
                    // Check if the day is a weekend
                    $isWeekend = in_array($dayOfWeek, ['Saturday', 'Sunday']);
                    
                    return $isWeekend ? 'Rest Day' : 'Working Day';
                })
                ->weight(FontWeight::Bold)
                ->size(TextColumn\TextColumnSize::Large),
                TextColumn::make('Day')
                ->getStateUsing(function (TimeSheet $timesheet): string {
                    $date = Carbon::parse($timesheet->date);
                    $dayOfWeek = $date->format('l'); 
                    
                    $dayShortcuts = [
                        'Monday' => 'M',
                        'Tuesday' => 'T',
                        'Wednesday' => 'W',
                        'Thursday' => 'Th',
                        'Friday' => 'F',
                        'Saturday' => 'Sa',
                        'Sunday' => 'Su'
                    ];
                    
                    return $dayShortcuts[$dayOfWeek];
                })
                ->tooltip(function (TimeSheet $timesheet): string{
                    $date = Carbon::parse($timesheet->date);
                    $dayOfWeek = $date->format('l');
            
                    return $dayOfWeek;
                })
                ->sortable(),
                TextColumn::make('date')
                ->label('Date')->searchable()
                // ->getStateUsing(function (TimeSheet $timesheet): string {

                //     $date = Carbon::parse($timesheet->date);
                //     return $date->format('M d Y');
                // })
                ->sortable(), 
                TextColumn::make('shift_schedule'),
                TextColumn::make('in_location')->label('In Location')->placeholder('-'),
                ColumnGroup::make('Time Logs', [
                    TextColumn::make('time_in')
                    ->getStateUsing(function (TimeSheet $timesheet): string {
    
                        $date = Carbon::parse($timesheet->time_in);
                        return $date->format('H:i');
                    })->sortable(),
                    TextColumn::make('break_time_out')
                    ->getStateUsing(function (TimeSheet $timesheet): string {
    
                        $date = Carbon::parse($timesheet->break_time_out);
                        return $date->format('H:i');
                    })->sortable(),
                    TextColumn::make('break_time_in')
                    ->getStateUsing(function (TimeSheet $timesheet): string {
    
                        $date = Carbon::parse($timesheet->break_time_in);
                        return $date->format('H:i');
                    })->sortable(),
                    TextColumn::make('time_out')
                    ->getStateUsing(function (TimeSheet $timesheet): string {
    
                        $date = Carbon::parse($timesheet->time_out);
                        return $date->format('H:i');
                    })->sortable(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('out_location')->label('Out Location')->placeholder('-'),
                TextColumn::make('out_date')->label('Out Date')->placeholder('-'),
                TextColumn::make('time_in_2')
                ->toggleable(isToggledHiddenByDefault:true)               
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->time_in_2);
                    return $date->format('H:i');
                })->sortable(),
                TextColumn::make('time_out_2')
                ->toggleable(isToggledHiddenByDefault:true)               
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->time_out_2);
                    return $date->format('H:i');
                })->sortable(),
                TextColumn::make('late_arrival')
                ->label('Late Arrival')
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $shiftSchedule = explode(' - ', $timesheet->shift_schedule);
                    $shiftStart = Carbon::parse($shiftSchedule[0]);
                    $timeIn = Carbon::parse($timesheet->time_in);
                    $lateTimeInMinutes = 0;
                    if ($timeIn->greaterThan($shiftStart)) {
                        $lateTimeInMinutes = $shiftStart->diffInMinutes($timeIn);
                    }

                    $formated_late = gmdate('H:i', $lateTimeInMinutes * 60);
                    return $formated_late;
                }),
                TextColumn::make('early_departure')
                ->label('Early Departure')
                ->toggleable(isToggledHiddenByDefault:true)               
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $shiftSchedule = explode(' - ', $timesheet->shift_schedule);
                    $shiftEnd = Carbon::parse($shiftSchedule[1]);

                    if ($timesheet->time_out  == '00:00:00') {
                        // No time_out recorded, return default value or handle it as you want
                        return "00:00";
                    }

                    $timeOut = Carbon::parse($timesheet->time_out);
                    $earlyLeaveMinutes = 0;
                    if ($timeOut->lessThan($shiftEnd)) {
                        $earlyLeaveMinutes = $timeOut->diffInMinutes($shiftEnd);
                    }

                    $formated_late = gmdate('H:i', $earlyLeaveMinutes * 60);
                    return $formated_late;
                }),
                TextColumn::make('late_time')
                ->toggleable(isToggledHiddenByDefault:true)               
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->late_time);
                    return $date->format('H:i');
                })
                ->summarize(Summarizer::make()
                ->label('')
                ->using(function (Builder $query): string {
                    return $query->selectRaw("TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(late_time))), '%H:%i') AS late_time")->value('late_time');
                }))
                ->sortable(),
                TextColumn::make('over_time')     
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->over_time);
                    return $date->format('H:i');
                })
                ->summarize(Summarizer::make()
                ->label('')
                ->using(function (Builder $query): string {
                    return $query->selectRaw("TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(over_time))), '%H:%i') AS over_time")->value('over_time');
                }))
            ])
            ->striped()
            ->defaultSort('date', 'desc')
            ->paginated([31, 'all'])
            ->filters([
                SelectFilter::make('year')
                ->label('Year')
                ->options($this->generateYearOptions())
                ->default(date('Y'))
                ->query(function ($query, array $data) {

                    if (isset($data['value'])) {
                        $query->whereYear('date', $data['value']);
                    }

                    $query->where('employee_id', $this->record->employee_id);
                }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            // ], layout: FiltersLayout::AboveContent)
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])
            ->recordClasses(fn (TimeSheet $timesheet) => match (Carbon::parse($timesheet->date)->format('l')) {
                // need to add the tailwind css for this to work
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' => 'border-s-2 border-green-600 dark:border-green-300',
                'Saturday', 'Sunday' => 'border-s-2 border-red-600 dark:border-red-300',
                default => null,
            });
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

    public function render() : View
    {
        return view('livewire.employee-time-sheets');
    }
    
}
