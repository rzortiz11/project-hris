<?php

namespace App\Livewire;

use App\Models\TimeLog;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

class EmployeeTimeLogs extends Component implements HasForms, HasTable
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
            ->query(TimeLog::query()->where('employee_id', $employee_id))
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
                ->weight(FontWeight::Bold)
                ->size(TextColumn\TextColumnSize::Large),
                TextColumn::make('date')
                ->label('Date')->searchable()
                ->sortable(), 
                TextColumn::make('day')->searchable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('time')
                ->getStateUsing(function (TimeLog $time_log): string {
                    $date = Carbon::parse($time_log->time);
                    return $date->format('H:i A');
                })->sortable(),
                TextColumn::make('location')->label('Location')->placeholder('-'),
                TextColumn::make('latitude')->placeholder('-'),
                TextColumn::make('longitude')->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
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

    public function render()
    {
        return view('livewire.employee-time-logs');
    }
}
