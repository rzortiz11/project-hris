<?php

namespace App\Livewire;

use App\Models\EmployeeLeaveApprover;
use App\Models\EmployeeLeaveBalance;
use App\Models\Leave;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Livewire\Component;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;
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

                
                Grid::make()
                ->schema([

                    Grid::make([
                        'default' => 1
                    ])
                    ->columnSpan(8)
                    ->schema([
                        Section::make("EMPLOYEE LEAVE APPROVER'S")
                        ->description('LEAVE APPROVERS')
                        ->icon('heroicon-o-document-duplicate')
                        ->id('createLeaveSection')
                        ->schema([
                            Select::make('type')->label('Leave Type')
                            ->required()
                            ->options(EmployeeLeaveBalance::all()->pluck('type','leave_balance_id')->map(function ($type) {
                                return ucwords(strtolower($type));
                            })->toArray())
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('balance', 0);
                                $set('used_balance', 0);
                                $set('remaining_balance', 0);
                            })
                            ->live(),
                            Grid::make([
                                'default' => 1
                            ])
                            ->schema([
                                DatePicker::make('from')
                                ->required()
                                ->label('From')
                                ->suffixIcon('heroicon-o-calendar-days')
                                ->minDate(now()),
                                DatePicker::make('to')
                                ->required()
                                ->label('To')
                                ->suffixIcon('heroicon-o-calendar-days')
                                ->minDate(now()),   
                            ])
                            ->columns(2),    
                            Textarea::make('remarks')->label('Remarks')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->autosize()
                            ->rows(5),
                            Select::make('approver_id')->label('Approver')
                            ->required()
                            ->options(EmployeeLeaveApprover::all()->pluck('approver_id','leave_approver_id')->map(function ($approver_id) {
            
                                $approver = User::find($approver_id);
                                return $approver ? ucwords(strtolower($approver->name)) : '';
                            })->toArray()),
                            Repeater::make('leave_documents')
                            ->label('')
                            ->relationship()
                            ->simple(
                                FileUpload::make('path')
                                ->panelAspectRatio('10:1')
                                ->label('')
                                ->disk('public')
                                ->directory('document/attachments')  
                                ->storeFileNamesIn('filename')                          
                                ->previewable()
                                ->openable()
                                ->downloadable()
                            )
                            ->addActionLabel('New Attachment')   
                        ])
                        ->footerActions([
                            Action::make('Submit')
                                ->action(function () {
                                    self::create();
                                }),
                        ])
                        ->footerActionsAlignment(Alignment::End)
                    ]),
                    Section::make('WIDGET OR STATISTICS ALLOCATION DETAILS')
                        ->description('LEAVE ALLOCATIONS')
                        ->icon('heroicon-o-chart-pie')
                        ->columnSpan(4)
                        ->schema([
                           TextInput::make('balance')->label('Allocated Leave Balance')->readOnly(),
                           TextInput::make('used_balance')->readOnly(),
                           TextInput::make('remaining_balance')->readOnly(),
                        ]),
                ])
                ->columns(12),
            ])
            ->statePath('data')
            ->model(Leave::class);
    }

    public function create()
    {
        $data = $this->form->getState();

        $employee = auth()->user()->employee;
        $data['employee_id'] = $employee->employee_id;

        $record = Leave::create($data);

        $this->form->model($record)->saveRelationships();
        return redirect()->to('admin/leave-self-services/leave/view');
    }

    public function render(): View
    {
        return view('livewire.create-leave-form');
    }
}