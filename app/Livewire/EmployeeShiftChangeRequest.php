<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\ShiftChangeRequest;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class EmployeeShiftChangeRequest extends Component implements HasForms, HasTable
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
        return $table
            ->query(ShiftChangeRequest::query()->where('approver_id', $this->record->user_id))
            ->columns([
                Split::make([
                    TextColumn::make('created_at')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('F d, Y');
                    })
                    ->sortable(),
                    ImageColumn::make('avatar')
                    ->grow(false)
                    ->getStateUsing(function (ShiftChangeRequest $data): string {
                        
                        // enchangement declare the relationship on model and just call it employee.picture
                        $employee = Employee::find($data->employee_id);
                        return isset($employee->picture) ? $employee->picture : '';
                    })
                    ->circular(),
                    Stack::make([
                        TextColumn::make('employee_id')
                        ->weight(FontWeight::Bold)
                        ->getStateUsing(function (ShiftChangeRequest $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee->user->name) ? $employee->user->name : '';
                        }), 
                        TextColumn::make('employee_reference')
                        ->getStateUsing(function (ShiftChangeRequest $data): string {
        
                            $employee = Employee::find($data->employee_id);
                            return isset($employee) ? $employee->employee_reference : '';
                        })
                    ]),
                    Stack::make([
                        Split::make([
                            TextColumn::make('old_time_in')
                            ->formatStateUsing(function ($state) {
                                return 'Old Shift Schedule :';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('old_time_in')
                            ->getStateUsing(function ($record) {
                                return $record->old_time_in ? Carbon::parse($record->old_time_in)->format('h:i A') : '00:00';
                            })
                            ->grow(false),
                            TextColumn::make('old_time_out')
                            ->formatStateUsing(function ($state) {
                                return '-';
                            })
                            ->grow(false)
                            ->weight(FontWeight::Bold),
                            TextColumn::make('old_time_out')
                            ->getStateUsing(function ($record) {
                                return $record->old_time_out ? Carbon::parse($record->old_time_out)->format('h:i A') : '00:00';
                            })->grow(false),
                        ]),
                        Stack::make([
                            Split::make([
                                TextColumn::make('new_time_in')
                                ->formatStateUsing(function ($state) {
                                    return 'Revised Shift Schedule :';
                                })
                                ->grow(false)
                                ->weight(FontWeight::Bold),
                                TextColumn::make('new_time_in')
                                ->getStateUsing(function ($record) {
                                    return $record->new_time_in ? Carbon::parse($record->new_time_in)->format('h:i A') : '00:00';
                                })
                                ->grow(false),
                                TextColumn::make('new_time_out')
                                ->formatStateUsing(function ($state) {
                                    return '-';
                                })
                                ->grow(false)
                                ->weight(FontWeight::Bold),
                                TextColumn::make('new_time_in')
                                ->getStateUsing(function ($record) {
                                    return $record->new_time_out ? Carbon::parse($record->new_time_out)->format('h:i A') : '00:00';
                                })
                                ->grow(false),
                            ]),
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
                ->alignCenter(),
                ])
                ->from('lg'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->color('primary'),
                Tables\Actions\Action::make('Approved')
                ->color('success')
                ->icon('heroicon-s-check-circle')
                ->action(function (ShiftChangeRequest $record, array $data) {
                    
                    $employee = Employee::find($record['employee_id']);
            
                    $record['status'] = 'approved';
                    $result = $record->save();

                    if($result){
                        self::approvedRequestNotification($employee->user);
                    }
                })
                ->disabled(fn (ShiftChangeRequest $record) => self::isActionAvailable($record))
                ->requiresConfirmation(),
                Tables\Actions\Action::make('Disapproved')
                ->color('danger')
                ->icon('heroicon-s-x-circle')
                ->action(function (ShiftChangeRequest $record, array $data) {
                    
                    $employee = Employee::find($record['employee_id']);

                    $record['status'] = 'denied';
                    $result = $record->save();

                    if($result){
                        self::deniedRequestNotification($employee->user);
                    }
                })
                ->disabled(fn (ShiftChangeRequest $record) => self::isActionAvailable($record))
                ->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5);
    }

    public static function approvedRequestNotification($recipient){

        Notification::make()
        ->success()
        ->title('Shift Change Request Approved')
        ->body('Approved Successfully.')
        ->send();

        Notification::make()
            ->title('Shift Change Request Approved')
            ->body('Your request has been approved.')
            ->success()
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Shift Change Request Approved')
        ->body('Your request has been approved.')
        ->seconds(5)
        ->success()
        ->broadcast($recipient);
    }

    public static function deniedRequestNotification($recipient){

        Notification::make()
        ->success()
        ->title('Shift Change Request Denied')
        ->body('Denied Successfully.')
        ->send();

        Notification::make()
            ->title('Shift Change Request Denied')
            ->body('Your request has been denied.')
            ->danger()
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Shift Change Request Denied')
        ->body('Your request has been denied.')
        ->seconds(5)
        ->danger()
        ->broadcast($recipient);
    }

    public static function isActionAvailable(ShiftChangeRequest $record): bool {

        if ($record->status == "void" || $record->status == "denied" || $record->status == "approved") {
            return true;
        }
    
        return false;
    }

    public function render(): View
    {
        return view('livewire.employee-shift-change-request');
    }
}
