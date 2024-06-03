<?php

namespace App\Livewire;

use App\Filament\Resources\EmployeeLeaveServiceResource\Widgets\LeaveSelfServiceAllocationPieChart;
use App\Models\EmployeeLeaveApprover;
use App\Models\EmployeeLeaveBalance;
use App\Models\Leave;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;

class CreateLeaveForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public $employee_id = "";
    public function mount(): void
    {
        $employee = auth()->user()->employee;
        $this->employee_id = $employee->employee_id;
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

                                $leave_balance_id = $get('type');
                                $this->dispatch('updateAllocationPieChart', $leave_balance_id);
                                // $Leavebalance = EmployeeLeaveBalance::find($leave_balance_id);

                                // $set('balance', $Leavebalance->balance);
                                // $set('used_balance', $Leavebalance->used_balance);
                                // $set('remaining_balance', $Leavebalance->remaining_balance);
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
                    Section::make('ALLOCATION DETAILS')
                        ->description('LEAVE ALLOCATIONS')
                        ->icon('heroicon-o-chart-pie')
                        ->columnSpan(4)
                        ->schema([
                           Livewire::make(LeaveSelfServiceAllocationPieChart::class)->lazy(),
             
                        //    Grid::make([
                        //     'default' => 1
                        //     ])
                        //     ->schema([
                        //         TextInput::make('balance')->label('Allocated')->readOnly(),
                        //         TextInput::make('used_balance')->label('Used')->readOnly(),
                        //         TextInput::make('remaining_balance')->label('Remaining')->readOnly(),
                        //     ])
                        //     ->columns(3),
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

        // Reinitialize the form to clear its data.
        $this->form->fill();

        Notification::make()
        ->success()
        ->title('Leave request created successfully')
        ->send();

        // return redirect()->to('admin/leave-self-services/leave/view');
    }

    public function render(): View
    {
        return view('livewire.create-leave-form');
    }
}