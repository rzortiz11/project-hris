<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Leave;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class EmployeeOnLeaveTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Leave::query())
            ->columns([
                TextColumn::make('employee_id')->searchable()
                ->label('Employee ID')
                ->getStateUsing(function (Leave $data): string {
                    $employee = Employee::find($data->employee_id);
                    
                    return $employee ? ucwords(strtolower($employee->employee_reference)) : '';
                }),
                TextColumn::make('employee_name')
                ->label('Name')
                ->getStateUsing(function (Leave $data): string {
                    $employee = Employee::find($data->employee_id);
                    
                    return $employee ? ucwords(strtolower($employee->user->name)) : '';
                }),
                TextColumn::make('department')
                ->label('Department')
                ->getStateUsing(function (Leave $data): string {
                    $employee = Employee::find($data->employee_id);
                    
                    return $employee ? $employee->position->reporting_designation : '';
                }),
                TextColumn::make('from'),
                TextColumn::make('to'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc');
    }

    public function render(): View
    {
        return view('livewire.employee-on-leave-table');
    }
}
