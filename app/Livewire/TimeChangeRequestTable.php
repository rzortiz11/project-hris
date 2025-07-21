<?php

namespace App\Livewire;

use App\Models\EmployeeRequestApprover;
use App\Models\TimeChangeRequest;
use App\Models\TimeSheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Collection;

class TimeChangeRequestTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TimePicker::make('old_time_in')
                ->seconds(false)
                ->readOnly()
                ->label('Time-in'),
            ])
            ->statePath('data')
            ->model(TimeChangeRequest::class);
    }

    public function table(Table $table): Table
    {
        $employee_id = $this->record->employee_id;
        
        return $table
            ->query(TimeChangeRequest::query()->where('employee_id', $employee_id))
            ->columns([
                TextColumn::make('time_change_id')->label('ID'),
                TextColumn::make('date_filling')->searchable(),
                TextColumn::make('type'),
                ColumnGroup::make('Old Time IN/OUT', [
                    TextColumn::make('old_time_in')
                    ->getStateUsing(function ($record) {
                        return $record->old_time_in ? Carbon::parse($record->old_time_in)->format('h:i A') : '00:00';
                    }),
                    TextColumn::make('old_time_out')
                    ->getStateUsing(function ($record) {
                        return $record->old_time_out ? Carbon::parse($record->old_time_out)->format('h:i A') : '00:00';
                    }),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                ColumnGroup::make('Revised Time IN/OUT', [
                    TextColumn::make('new_time_in')
                    ->getStateUsing(function ($record) {
                        return $record->new_time_in ? Carbon::parse($record->new_time_in)->format('h:i A') : '00:00';
                    }),
                   TextColumn::make('new_time_out')
                    ->getStateUsing(function ($record) {
                        return $record->new_time_out ? Carbon::parse($record->new_time_out)->format('h:i A') : '00:00';
                    }),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
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
                ->getStateUsing(function (TimeChangeRequest $record): string {

                    $approver = User::find($record->approver_id);
                    return $approver ? ucwords(strtolower($approver->name)) : '';
                })
                ,
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->mutateFormDataUsing(function (array $data) use ($employee_id): array {
                    $data['employee_id'] = $employee_id;
             
                    return $data;
                })
                ->label('Request Time Change')
                ->model(TimeChangeRequest::class)
                ->form([
                    self::timeChangeForm($employee_id)
                ])
                ->after(function ($record) {

                    $recipient = User::find($record['approver_id']);

                    self::sendRequestNotification($recipient);
                })
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->form([
                    self::timeChangeForm($employee_id)
                ])
                ->disabled(fn (TimeChangeRequest $record) => self::isActionAvailable($record)),
                Tables\Actions\Action::make('Void')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(function (TimeChangeRequest $record, array $data) {
                    
                    $record['status'] = 'void';
                    $record->save();
                })->requiresConfirmation()
                ->disabled(fn (TimeChangeRequest $record) => self::isActionAvailable($record))
            ])
            ->defaultPaginationPageOption(5)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Void Request')
                    ->color('danger')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->action(function (Collection $records) {

                        $records->each(function ($record){
                            $record['status'] = 'void';
                            $record->save();
                        });
                    })->requiresConfirmation(),
                ]),
            ])->checkIfRecordIsSelectableUsing(fn (TimeChangeRequest $record) => self::isActionAvailable($record));
    }

    public static function timeChangeForm($employee_id): Grid
    {
        return Grid::make([
            'default' => 1
        ])
        ->schema([
            DatePicker::make('date_filling')
            ->label('Date')
            ->suffixIcon('heroicon-o-calendar-days')
            ->maxDate(now())
            ->afterStateUpdated(function (Get $get, Set $set) use ($employee_id) {

                $date = $get('date_filling');
                $formattedDate = Carbon::parse($date)->format('Y-m-d'); // Ensure the date is formatted correctly
        
                $timesheet = TimeSheet::where('employee_id', $employee_id)->where('date', $formattedDate)->first();
                if ($timesheet) {
                    $timeIn = $timesheet->time_in == '00:00:00' ? "" : Carbon::parse($timesheet->time_in)->format('H:i');
                    $timeOut = $timesheet->time_out == '00:00:00' ? "" : Carbon::parse($timesheet->time_out)->format('H:i');
        
                    $set('old_time_in', $timeIn);
                    $set('old_time_out', $timeOut);
                    $set('new_time_in', $timeIn);
                    $set('new_time_out', $timeOut);
                } else {
                    $set('old_time_in', '00:00');
                    $set('old_time_out', '00:00');
                    $set('new_time_in', '00:00');
                    $set('new_time_out', '00:00');
                }
            })->live(),
            Split::make([
                Section::make('Current Time Entry')
                ->description('')
                ->icon('heroicon-o-clock')
                ->schema([
                    TimePicker::make('old_time_in')
                    ->seconds(false)
                    ->readOnly()
                    ->label('Time-in'),
                    TimePicker::make('old_time_out')
                    ->readOnly()
                    ->seconds(false)
                    ->label('Time-out'),
                ])->columns(),
                Section::make('Revised Time Entry')
                ->description('')
                ->icon('heroicon-s-clock')
                ->schema([
                    TimePicker::make('new_time_in')
                    ->required()
                    ->seconds(false)
                    ->label('Time-in'),
                    TimePicker::make('new_time_out')
                    ->required()
                    ->seconds(false)
                    ->label('Time-out'),
                ])->columns(),
            ])->from('lg'),
            Select::make('type')->options([
                '1' => 'Time Correction',
                '2' => 'Official Business'
            ]),
            Textarea::make('remarks')->label('Remarks')
            ->required()
            ->minLength(2)
            ->maxLength(255)
            ->autosize()
            ->rows(5),
            Select::make('approver_id')->label('Approver')
            ->required()
            ->options(EmployeeRequestApprover::all()->pluck('approver_id','approver_id')->map(function ($approver_id) {

                $approver = User::find($approver_id);
                return $approver ? ucwords(strtolower($approver->name)) : '';
            })->toArray())
            ->label('Approver')
            ->preload()
            ->required()
        ]);
    }

    public static function sendRequestNotification($recipient){

        Notification::make()
            ->title('Time Change Request')
            ->body('Employee '.$recipient->name. ' applied for Time Change request')
            ->icon('heroicon-o-inbox-arrow-down')
            ->info()
            ->actions([
                Action::make('view')
                    ->button()
                    ->color('success')
                    ->url(route('filament.admin.pages.employee-request','tab=-time-change-request-tab'), shouldOpenInNewTab: true)
            ])
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('Time Change Request')
        ->icon('heroicon-o-inbox-arrow-down')
        ->body('Employee '.$recipient->name. ' applied for Time Change request')
        ->seconds(5)
        ->actions([
            Action::make('view')
                ->button()
                ->color('success')
                ->url(route('filament.admin.pages.employee-request','tab=-time-change-request-tab'), shouldOpenInNewTab: true)
        ])
        ->info()
        ->broadcast($recipient);
    }

    public static function isActionAvailable(TimeChangeRequest $record): bool {

        if ($record->status == "void" || $record->status == "denied" || $record->status == "approved") {
            return true;
        }
    
        return false;
    }

    public function render(): View
    {
        return view('livewire.time-change-request-table');
    }
}
