<?php

namespace App\Livewire;

use App\Models\EmployeeEmploymentDetail;
use App\Models\EmployeeLeaveApprover;
use App\Models\ShiftChangeRequest;
use App\Models\TimeSheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShiftChangeRequestTable extends Component implements HasForms, HasTable
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
        $employee_id = $this->record->employee_id;

        return $table
            ->query(ShiftChangeRequest::query()->where('employee_id', $employee_id))
            ->columns([
                TextColumn::make('shift_change_id'),
                ColumnGroup::make('Old Shift Schedule', [
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
                ColumnGroup::make('Revised Shift Schedule', [
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
                ->getStateUsing(function (ShiftChangeRequest $record): string {

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
                TextColumn::make('created_at')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->mutateFormDataUsing(function (array $data) use ($employee_id): array {
                    $data['employee_id'] = $employee_id;
             
                    return $data;
                })
                ->label('Request Shift Change')
                ->model(ShiftChangeRequest::class)
                ->form([
                    self::shiftChangeForm($employee_id)
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->form([
                    self::shiftChangeForm($employee_id)
                ])
                ->visible(fn (ShiftChangeRequest $record) => self::isActionAvailable($record)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Void')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(function (ShiftChangeRequest $record, array $data) {
                    
                    $record['status'] = 'void';
                    $record->save();
                })->requiresConfirmation()
                ->visible(fn (ShiftChangeRequest $record) => self::isActionAvailable($record))
            ])
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
            ])->checkIfRecordIsSelectableUsing(fn (ShiftChangeRequest $record) => self::isActionAvailable($record));
    }

    
    public static function shiftChangeForm($employee_id): Grid
    {
        $current_shift = EmployeeEmploymentDetail::where('employee_id', $employee_id)->first();

        return Grid::make([
            'default' => 1
        ])
        ->schema([
            Section::make('Current Time Shift')
            ->description('')
            ->icon('heroicon-o-clock')
            ->schema([
                TimePicker::make('old_time_in')
                ->default(function () use ($current_shift) {

                    return $current_shift->time_in;
                })
                ->seconds(false)
                ->readOnly()
                ->label('Time-in'),
                TimePicker::make('old_time_out')
                ->default(function () use ($current_shift) {

                        return $current_shift->time_out;
                })
                ->readOnly()
                ->seconds(false)
                ->label('Time-out'),
            ])->columns(),
            Section::make('Revised Time Shift')
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
            })->toArray())
            ->label('Approver')
            ->preload()
            ->required()
        ]);
    }

    public static function isActionAvailable(ShiftChangeRequest $record): bool {

        if ($record->status == "void") {
            return false;
        }
    
        return true;
    }

    public function render(): View
    {
        return view('livewire.shift-change-request-table');
    }
}
