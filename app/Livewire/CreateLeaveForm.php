<?php

namespace App\Livewire;

use App\Models\EmployeeLeaveApprover;
use App\Models\EmployeeLeaveBalance;
use App\Models\Leave;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class CreateLeaveForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $employee = auth()->user()->employee;
        $employee->employee_id;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')->label('Leave Type')
                ->required()
                ->options(EmployeeLeaveBalance::all()->pluck('type','leave_balance_id')->map(function ($type) {
                    return ucwords(strtolower($type));
                })->toArray())
                ->searchable()
                ->preload(),
                Textarea::make('remarks')->label('Remarks')
                ->minLength(2)
                ->maxLength(255)
                ->autosize()
                ->rows(10)
                ->cols(20),
                Select::make('approver_id')->label('Approver')
                ->required()
                ->options(EmployeeLeaveApprover::all()->pluck('approver_id','leave_approver_id')->map(function ($approver_id) {

                    $approver = User::find($approver_id);
                    return $approver ? ucwords(strtolower($approver->name)) : '';
                })->toArray())
                ->searchable()
                ->preload()
            ])
            ->statePath('data')
            ->model(Leave::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Leave::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.create-leave-form');
    }
}