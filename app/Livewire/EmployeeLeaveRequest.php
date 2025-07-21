<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class EmployeeLeaveRequest extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        if($this->record != null){
            $leave_query = Leave::query()->where('approver_id', $this->record->user_id);
        } else {
            $leave_query = Leave::query();
        }

        return $table
            ->query($leave_query)
            ->columns([
                Split::make([
                    TextColumn::make('date_filling')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('F d, Y');
                    })
                    ->sortable(),
                    ImageColumn::make('avatar')
                    ->grow(false)
                    ->getStateUsing(function (Leave $data): string {
    
                        $employee = Employee::find($data->employee_id);
                        return isset($employee->picture) ? $employee->picture : '';
                    })
                    ->circular(),
                    Stack::make([
                        TextColumn::make('employee_id')
                        ->weight(FontWeight::Bold)
                        ->getStateUsing(function (Leave $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee->user->name) ? $employee->user->name : '';
                        }), 
                        TextColumn::make('employee_reference')
                        ->getStateUsing(function (Leave $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee) ? $employee->employee_reference : '';
                        }), 
                    ]),
                    Stack::make([
                        Split::make([
                            TextColumn::make('from')
                            ->formatStateUsing(function ($state) {
                                return 'From :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('from')
                            ->formatStateUsing(function ($state) {
                                return Carbon::parse($state)->format('F d, Y');
                            })
                            ->grow(false),
                            TextColumn::make('to')
                            ->formatStateUsing(function ($state) {
                                return 'To :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('to')
                            ->formatStateUsing(function ($state) {
                                return Carbon::parse($state)->format('F d, Y');
                            })->grow(false),
                        ]),
                        Split::make([
                            TextColumn::make('hours')
                            ->formatStateUsing(function ($state) {
                                return 'Hours :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('hours')
                            ->grow(false),
                        ]),
                        Split::make([
                            TextColumn::make('remarks')
                            ->formatStateUsing(function ($state) {
                                return 'Remarks :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('remarks')
                            ->limit(10)
                            ->tooltip(function (TextColumn $column): ?string {
                                $state = $column->getState();
                        
                                if (strlen($state) <= $column->getCharacterLimit()) {
                                    return null;
                                }
                        
                                return $state;
                            }), 
                        ]),
                        Split::make([
                            TextColumn::make('approver_id')
                            ->formatStateUsing(function ($state) {
                                return 'Approver :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('approver_id')
                            ->formatStateUsing(function ($state) {
                                $approver_id = $state;
                                $user = User::find($approver_id);
                                return $user ? ucwords(strtolower($user->name)) : '';
                            }), 
                        ])
                        ->visible($this->record == null ? true : false),   
                    ])
                    ->alignment(Alignment::Start)
                    ->grow(false),
                    TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'denied' => 'danger',
                        'void' => 'danger',
                    })
                    ->sortable()
                    ->alignCenter(),
                    ])
                ->from('lg'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'denied' => 'Denied',
                    'void' => 'Void',
                ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->color('primary')
                ->infolist(fn(Infolist $infolist) => EmployeeLeaveRequest::infolist($infolist)),
                Tables\Actions\Action::make('Approved')
                ->color('success')
                ->icon('heroicon-s-check-circle')
                ->action(function (Leave $record, array $data) {
                    
                    $employee = Employee::find($record['employee_id']);
                    $work_schedule = $employee->employment->work_schedule;
                    $fromDate = Carbon::parse($record->from); // Start date, e.g., "2024-11-19"
                    $toDate = Carbon::parse($record->to); // End date, e.g., "2024-11-22"
                    
                    // Generate the range of dates
                    $period = CarbonPeriod::create($fromDate, $toDate);
                    
                    // Initialize leave days counter
                    $leaveDaysUsed = 0;
                    
                    // Iterate through the date range
                    foreach ($period as $date) {
                        // Check if the day is in the work schedule
                        if (in_array(strtolower($date->format('l')), $work_schedule)) {
                            $leaveDaysUsed++;
                        }
                    }
                    
                    $leave_balance = EmployeeLeaveBalance::where('leave_balance_id',$record->leave_balance_id)->first();
                    if ($leave_balance) {
                        $leave_balance->used_balance += $leaveDaysUsed;
                        $leave_balance->remaining_balance = $leave_balance->balance - $leave_balance->used_balance; 
                        if ($leave_balance->remaining_balance < 0) {
                            $leave_balance->remaining_balance = 0;
                        }
                    
                        $leave_balance->save();
                    }

                    $record['status'] = 'approved';
                    $result = $record->save();

                    if($result){
                        self::approvedRequestNotification($employee->user);
                    }
                })
                ->hidden($this->record == null ? true : false)
                ->disabled(fn (Leave $record) => self::isActionAvailable($record))
                ->requiresConfirmation(),
                Tables\Actions\Action::make('Disapproved')
                ->color('danger')
                ->icon('heroicon-s-x-circle')
                ->hidden($this->record == null ? true : false)
                ->action(function (Leave $record, array $data) {
                    
                    $employee = Employee::find($record['employee_id']);

                    $record['status'] = 'denied';
                    $result = $record->save();

                    if($result){
                        self::deniedRequestNotification($employee->user);
                    }
                })
                ->disabled(fn (Leave $record) => self::isActionAvailable($record))
                ->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make()
                ->schema([
                    Infolists\Components\TextEntry::make('type')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('job_position')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('date_filling')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('from')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('to')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('hours')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('is_paid')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('remarks')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('status')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary'),
                    Infolists\Components\TextEntry::make('created_at')
                    ->weight(FontWeight::Bold)
                    ->size(TextEntry\TextEntrySize::Large)
                    ->color('primary')
                    ->date(),
                ])->columns(3)
            ]);
    }

    public static function approvedRequestNotification($recipient){

        Notification::make()
        ->success()
        ->title('Leave Request Approved')
        ->body('Approved Successfully.')
        ->send();

        Notification::make()
            ->title('Leave Request Approved')
            ->body('Your request has been approved.')
            ->success()
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Leave Request Approved')
        ->body('Your request has been approved.')
        ->seconds(5)
        ->success()
        ->broadcast($recipient);
    }

    public static function deniedRequestNotification($recipient){

        Notification::make()
        ->success()
        ->title('Leave Request Denied')
        ->body('Denied Successfully.')
        ->send();

        Notification::make()
            ->title('Leave Request Denied')
            ->body('Your request has been denied.')
            ->danger()
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Leave Request Denied')
        ->body('Your request has been denied.')
        ->seconds(5)
        ->danger()
        ->broadcast($recipient);
    }

    public static function isActionAvailable(Leave $record): bool {

        if ($record->status == "void" || $record->status == "denied" || $record->status == "approved") {
            return true;
        }
    
        return false;
    }

    public function render(): View
    {
        return view('livewire.employee-leave-request');
    }
}
