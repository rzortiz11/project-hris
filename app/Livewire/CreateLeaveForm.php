<?php
// THIS IS NOT BEING USED ATM
// namespace App\Livewire;

// use App\Filament\Resources\EmployeeLeaveServiceResource\Widgets\LeaveSelfServiceAllocationPieChart;
// use App\Models\EmployeeLeaveApprover;
// use App\Models\EmployeeLeaveBalance;
// use App\Models\EmployeeRequestApprover;
// use App\Models\Leave;
// use App\Models\User;
// use Carbon\Carbon;
// use Carbon\CarbonPeriod;
// use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\FileUpload;
// use Filament\Forms\Components\Grid;
// use Filament\Forms\Components\Repeater;
// use Filament\Forms\Components\Select;
// use Filament\Forms\Components\Textarea;
// use Filament\Forms\Concerns\InteractsWithForms;
// use Filament\Forms\Contracts\HasForms;
// use Filament\Forms\Form;
// use Filament\Forms\Get;
// use Livewire\Component;
// use Filament\Forms\Components\Actions\Action;
// use Filament\Forms\Components\Livewire;
// use Filament\Forms\Components\Section;
// use Filament\Forms\Components\Split;
// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Set;
// use Filament\Support\Enums\Alignment;
// use Illuminate\Contracts\View\View;
// use Filament\Notifications\Notification;
// use Illuminate\Support\Str;

// class CreateLeaveForm extends Component implements HasForms
// {
//     use InteractsWithForms;

//     public ?array $data = [];

//     public $employee_id = "";
//     public function mount(): void
//     {
//         $employee = auth()->user()->employee;
//         $this->employee_id = $employee->employee_id;
//     }

//     public function form(Form $form): Form
//     {
//         $record = $this->employee;
//         return $form
//             ->schema([
//                 Grid::make()
//                 ->schema([
//                     Split::make([
//                         Grid::make([
//                             'default' => 1
//                         ])
//                         ->columnSpan(8)
//                         ->schema([
//                             Section::make("EMPLOYEE LEAVE FORM")
//                             ->description('LEAVE FORM')
//                             ->icon('heroicon-o-document-duplicate')
//                             ->id('createLeaveSection')
//                             ->schema([
//                                 Select::make('leave_balance_id')->label('Leave Type')
//                                 ->required()
//                                 ->options(EmployeeLeaveBalance::all()->pluck('type','leave_balance_id')->map(function ($type) {
//                                     return ucwords(strtolower($type));
//                                 })->toArray())
//                                 ->afterStateUpdated(function (Get $get, Set $set) {
//                                     $leave_balance_id = $get('type');
//                                     $this->dispatch('updateAllocationPieChart', $leave_balance_id);
//                                     // $Leavebalance = EmployeeLeaveBalance::find($leave_balance_id);

//                                     // $set('balance', $Leavebalance->balance);
//                                     // $set('used_balance', $Leavebalance->used_balance);
//                                     // $set('remaining_balance', $Leavebalance->remaining_balance);
//                                 })
//                                 ->live(),
//                                 Grid::make([
//                                     'default' => 1
//                                 ])
//                                 ->schema([
//                                     DatePicker::make('from')
//                                     ->required()
//                                     ->label('From')
//                                     ->suffixIcon('heroicon-o-calendar-days')
//                                     ->minDate(now())
//                                     ->afterStateUpdated(function (Get $get,Set $set) use ($record){
//                                         // repeatitive will convert this to one function later on
//                                         $work_schedule = $record->employment->work_schedule;
//                                         $fromDate = Carbon::parse($get('from')); 
//                                         $toDate = Carbon::parse($get('to'));
                                        
//                                         $period = CarbonPeriod::create($fromDate, $toDate);
                                        
//                                         $leaveDaysUsed = 0;
                                        
//                                         foreach ($period as $date) {
//                                             if (in_array(strtolower($date->format('l')), $work_schedule)) {
//                                                 $leaveDaysUsed++;
//                                             }
//                                         }
                            
                                        
//                                         $convert_to_hours = $leaveDaysUsed * 8; // this is temporary x 8 hours as regular work hours
//                                         $set('hours',$convert_to_hours);
//                                     })
//                                     ->live(),
//                                     DatePicker::make('to')
//                                     ->required()
//                                     ->label('To')
//                                     ->suffixIcon('heroicon-o-calendar-days')
//                                     ->minDate(now())
//                                     ->afterStateUpdated(function (Get $get,Set $set) use ($record){
//                                         // repeatitive will convert this to one function later on
//                                         $work_schedule = $record->employment->work_schedule;
//                                         $fromDate = Carbon::parse($get('from')); 
//                                         $toDate = Carbon::parse($get('to'));
                                        
//                                         $period = CarbonPeriod::create($fromDate, $toDate);
                                        
//                                         $leaveDaysUsed = 0;
                                        
//                                         foreach ($period as $date) {
//                                             if (in_array(strtolower($date->format('l')), $work_schedule)) {
//                                                 $leaveDaysUsed++;
//                                             }
//                                         }
                            
                                        
//                                         $convert_to_hours = $leaveDaysUsed * 8; // this is temporary x 8 hours as regular work hours
//                                         $set('hours',$convert_to_hours);
//                                     })
//                                     ->live()
//                                 ])
//                                 ->columns(2),    
//                                 Textarea::make('remarks')->label('Remarks')
//                                 ->required()
//                                 ->minLength(2)
//                                 ->maxLength(255)
//                                 ->autosize()
//                                 ->rows(5),
//                                 Select::make('approver_id')->label('Approver')
//                                 ->required()
//                                 ->options(EmployeeRequestApprover::all()->pluck('approver_id','approver_id')->map(function ($approver_id) {
//                                     $approver = User::find($approver_id);
//                                     return $approver ? ucwords(strtolower($approver->name)) : '';
//                                 })->toArray()),
//                                 Repeater::make('leave_documents')
//                                 ->label('')
//                                 ->relationship()
//                                 ->simple(
//                                     FileUpload::make('path')
//                                     ->panelAspectRatio('10:1')
//                                     ->label('')
//                                     ->disk('public')
//                                     ->directory('document/attachments')  
//                                     ->storeFileNamesIn('filename')                          
//                                     ->previewable()
//                                     ->openable()
//                                     ->downloadable()
//                                 )
//                                 ->addActionLabel('New Attachment')   
//                             ])
//                             ->footerActions([
//                                 Action::make('Submit')
//                                     ->action(function () {
//                                         self::create();
//                                     }),
//                             ])
//                             ->footerActionsAlignment(Alignment::End)
//                         ]),
//                         Grid::make([
//                             'default' => 1
//                         ])
//                         ->columnSpan(4)
//                         ->schema([
//                             Section::make('ALLOCATION DETAILS')
//                             ->description('LEAVE ALLOCATIONS')
//                             ->icon('heroicon-o-chart-pie')
//                             ->schema([
//                             Livewire::make(LeaveSelfServiceAllocationPieChart::class)->key(self::generateUuid())->lazy()
                
//                             //    Grid::make([
//                             //     'default' => 1
//                             //     ])
//                             //     ->schema([
//                             //         TextInput::make('balance')->label('Allocated')->readOnly(),
//                             //         TextInput::make('used_balance')->label('Used')->readOnly(),
//                             //         TextInput::make('remaining_balance')->label('Remaining')->readOnly(),
//                             //     ])
//                             //     ->columns(3),
//                             ]),
//                         ])
//                     ])
//                     ->columnSpanFull()
//                     ->from('lg'),
//                 ]),
//             ])
//             ->statePath('data')
//             ->model(Leave::class);
//     }

//     public static function generateUuid()
//     {
//         return (string) Str::uuid();
//     }

//     public function create()
//     {
//         $data = $this->form->getState();
//         dump($data);
//         die;
//         $employee = auth()->user()->employee;
//         $data['employee_id'] = $employee->employee_id;

//         $record = Leave::create($data);

//         $this->form->model($record)->saveRelationships();

//         // Reinitialize the form to clear its data.
//         $this->form->fill();

//         Notification::make()
//         ->success()
//         ->title('Leave request created successfully')
//         ->send();

//         // return redirect()->to('admin/leave-self-services/leave/view');
//     }

//     public function render(): View
//     {
//         return view('livewire.create-leave-form');
//     }
// }