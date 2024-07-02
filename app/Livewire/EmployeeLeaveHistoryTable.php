<?php

namespace App\Livewire;

use App\Filament\Resources\EmployeeLeaveServiceResource\Widgets\LeaveSelfServiceAllocationPieChart;
use App\Models\Employee;
use App\Models\EmployeeLeaveApprover;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeSalaryDetail;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Components\Split as FormSplit;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Str;

class EmployeeLeaveHistoryTable extends Component implements HasForms, HasTable
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
        $employee_id = $this->record->employee_id;

        return $table
            // ->query(EmployeeSalaryDetail::query()->where('employee_id', $this->record->employee_id))
            ->relationship(fn (): HasMany => $this->record->employee_leaves())
            // ->inverseRelationship('employee')
            ->columns([
                TextColumn::make('leave_id')
                ->label('ID'),
                TextColumn::make('employee_id')->searchable()
                ->label('Employee ID'), 
                TextColumn::make('date_filling')
                ->sortable(),
                TextColumn::make('from')
                ->label('Leave From'),
                TextColumn::make('to')
                ->label('Leave to'),
                TextColumn::make('hours')
                ->label('Hours'),
                TextColumn::make('remarks')
                ->limit(10)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
             
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
             
                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
                TextColumn::make('approver_id')
                ->label('Approver')
                ->getStateUsing(function (Leave $data): string {
                    $approver = User::find($data->approver_id);
                    return $approver ? ucwords(strtolower($approver->name)) : '';
                }),
                TextColumn::make('action_date')
                ->label('Action Date'),
                TextColumn::make('status')
                ->color(fn (string $state): string => match($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'denied' => 'danger',
                    'void' => 'danger',
                })
                ->sortable()
                ->label('Status'),
            ])
            ->defaultPaginationPageOption(5)
            ->filters([
                // ...
            ])
            ->headerActions([
                CreateAction::make()
                ->mutateFormDataUsing(function (array $data) use ($employee_id): array {
                    $data['employee_id'] = $employee_id;
                    $data['date_filling'] = now();
             
                    return $data;
                })
                ->label('Request a Leave')
                ->model(Leave::class)
                ->form([
                    self::LeaveForm($employee_id)
                ])
                ->after(function ($record) {

                    $recipient = User::find($record['approver_id']);

                    self::sendRequestNotification($recipient);
                })->modalWidth('6xl'),
            ])
            ->actions([
                ViewAction::make()
                ->infolist(fn(Infolist $infolist) => EmployeeLeaveHistoryTable::infolist($infolist)),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public static function leaveForm() : FormGrid
    {
        return FormGrid::make()
        ->schema([
            FormSplit::make([
                FormGrid::make([
                    'default' => 1
                ])
                ->columnSpan(8)
                ->schema([
                    FormSection::make("EMPLOYEE LEAVE FORM")
                    ->description('LEAVE FORM')
                    ->icon('heroicon-o-document-duplicate')
                    ->id('createLeaveSection')
                    ->schema([
                        Select::make('type')->label('Leave Type')
                        ->required()
                        ->options(EmployeeLeaveBalance::all()->pluck('type','leave_balance_id')->map(function ($type) {
                            return ucwords(strtolower($type));
                        })->toArray())
                        ->afterStateUpdated(function (Get $get, Set $set, $livewire) {

                            $leave_balance_id = $get('type');
                            $livewire->dispatch('updateAllocationPieChart', $leave_balance_id);
                        })
                        ->live(),
                        FormGrid::make([
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
                ]),
                FormGrid::make([
                    'default' => 1
                ])
                ->columnSpan(4)
                ->schema([
                        FormSection::make('ALLOCATION DETAILS')
                        ->description('LEAVE ALLOCATIONS')
                        ->icon('heroicon-o-chart-pie')
                        ->schema([
                        Livewire::make(LeaveSelfServiceAllocationPieChart::class)->key(self::generateUuid())->lazy()
                    ]),
                ])
            ])
            ->columnSpanFull()
            ->from('lg'),
        ]);
    }    

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        Grid::make([
                            'default' => 1
                        ])
                        ->schema([
                            TextEntry::make('date_filling')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary')
                            ->date(),
                            TextEntry::make('from')
                            ->label('Leave From')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary')
                            ->date(),
                            TextEntry::make('to')
                            ->label('Leave To')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary')
                            ->date(),
                            TextEntry::make('hours')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                            TextEntry::make('remarks')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                            TextEntry::make('is_paid')
                            ->label('Leave With Pay')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                            TextEntry::make('approver_id')
                            ->label('Approver')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                            TextEntry::make('action_date')
                            ->label('Action Date')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary')
                            ->date(),
                            TextEntry::make('status')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                        ])->columns(3),
                        Grid::make([
                            'default' => 1
                        ])
                        ->schema([
                            TextEntry::make('disapproved_reason')
                            ->label('Disapproved Reason')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary'),
                            TextEntry::make('create_at')
                            ->label('Created Date and Time')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->color('primary')
                            ->date(),
                        ])->columns(2),
                    ]),
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        RepeatableEntry::make('leave_documents')
                        ->schema([
                            ImageEntry::make('path')
                            ->visibility('private')
                            ->disk('public')
                        ])
                        ->columns(1)
                    ])
                    ->grow(false),
                ])
            ]);
    }

    public static function sendRequestNotification($recipient){

        Notification::make()
            ->title('Leave Request')
            ->body('Employee '.$recipient->name. ' applied for Leave request')
            ->icon('heroicon-o-folder-open')
            ->info()
            ->actions([
                NotificationAction::make('view')
                    ->button()
                    ->color('success')
                    ->url(route('filament.admin.pages.employee-request','tab=-leave-request-tab'), shouldOpenInNewTab: true)
            ])
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Leave Request')
        ->icon('heroicon-o-folder-open')
        ->body('Employee '.$recipient->name. ' applied for Leave request')
        ->seconds(5)
        ->actions([
            NotificationAction::make('view')
                ->button()
                ->color('success')
                ->url(route('filament.admin.pages.employee-request','tab=-leave-request-tab'), shouldOpenInNewTab: true)
        ])
        ->info()
        ->broadcast($recipient);
    }

    public static function isActionAvailable(Leave $record): bool {

        if ($record->status == "void" || $record->status == "denied" || $record->status == "approved") {
            return true;
        }
    
        return false;
    }


    public function render()
    {
        return view('livewire.employee-leave-history-table');
    }
}
