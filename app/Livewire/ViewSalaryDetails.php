<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\EmployeeSalaryDetail;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

class ViewSalaryDetails extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
        
    public $record;

    public function mount(Employee $record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            // ->query(EmployeeSalaryDetail::query()->where('employee_id', $this->record->employee_id))
            ->relationship(fn (): HasMany => $this->record->salary())
            // ->inverseRelationship('employee')
            ->columns([
                TextColumn::make('type')
                ->label('Type')
                ->sortable(), 
                TextColumn::make('monthly_amount')
                ->label('Montly Amount')
                ->sortable()
                ->alignment('right')
                ->summarize(Sum::make()->label('Total Monthly')), 
                TextColumn::make('yearly_amount')
                ->label('Yearly Amount')
                ->sortable()
                ->alignment('right')
                ->summarize(Sum::make()->label('Total Yearly'))
            ])
            // ->poll('5s')
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

    public function render()
    {
        return view('livewire.view-salary-details');
    }
}
