<?php

namespace App\Livewire;

use App\Models\TimeSheet;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

class EmployeeTimeSheet extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
        
    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {

        $employee_id = $this->record->employee_id;
    
        return $table
            ->query(TimeSheet::query()->where('employee_id', $employee_id))
            ->columns([
                TextColumn::make('date_type')->label('')
                ->default('•')
                ->color(fn (string $state): string => match ($state) {
                    '•' => 'success',
                }),
                TextColumn::make('date')
                ->label('Date')->searchable()
                // ->getStateUsing(function (TimeSheet $timesheet): string {

                //     $date = Carbon::parse($timesheet->date);
                //     return $date->format('M d Y');
                // })
                ->sortable(), 
                TextColumn::make('shift_schedule'),
                TextColumn::make('in_location')->label('In Location')->placeholder('-'),
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
                TextColumn::make('out_location')->label('Out Location')->placeholder('-'),
                TextColumn::make('out_date')->label('Out Date')->placeholder('-'),
                TextColumn::make('time_in_2')
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->time_in_2);
                    return $date->format('H:i');
                })->sortable(),
                TextColumn::make('time_out_2')
                ->getStateUsing(function (TimeSheet $timesheet): string {

                    $date = Carbon::parse($timesheet->time_out_2);
                    return $date->format('H:i');
                })->sortable(),
                TextColumn::make('late_time')               
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
            ->defaultSort('date', 'desc')
            ->paginated(false)
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    
    public function render() : View
    {
        return view('livewire.employee-time-sheets');
    }
    
}
