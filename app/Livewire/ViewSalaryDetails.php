<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\EmployeeSalaryDetail;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Livewire;

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
                TextColumn::make('name')
                ->label('Name'), 
                TextColumn::make('type')
                ->label('Type')
                ->sortable(), 
                TextColumn::make('amount')
                ->label('Amount')
                ->sortable()
                ->alignment('right')
                ->summarize(Sum::make()->label('Total Monthly')), 
                CheckboxColumn::make('is_taxable')->disabled()->alignment('center'),
                TextColumn::make('pay_period'),
                TextColumn::make('effective_date'),
                TextColumn::make('expiration_date'),

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

    public function afterSave()
    {
       // emit and render or refresh this page
    }

    public function render()
    {
        return view('livewire.view-salary-details');
    }
}
