<?php

namespace App\Livewire;

use App\Models\PayPeriod;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class EditPayPeriodForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public PayPeriod $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->schema([
                    Select::make('type')->label('Type')
                    ->required()
                    ->options([
                        'weekly' => 'Weekly',
                        'biweekly' => 'Bi-Weekly',
                        'monthly' => 'Monthly'
                    ]),
                    DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date')
                    ->suffixIcon('heroicon-o-calendar-days'),
                    DatePicker::make('end_date')
                    ->required()
                    ->label('End Date')
                    ->suffixIcon('heroicon-o-calendar-days'),   
                    DatePicker::make('cut_off_date')
                    ->required()
                    ->label('Cut Off Date')
                    ->suffixIcon('heroicon-o-calendar-days'), 
                ])->columns(4)
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
    }

    public function render(): View
    {
        return view('livewire.edit-pay-period-form');
    }
}
