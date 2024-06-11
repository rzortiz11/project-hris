<?php

namespace App\Livewire;

use App\Models\EmployeeLeaveApprover;
use App\Models\UnderTimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UnderTimeRequestTable extends Component implements HasForms, HasTable
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
            ->query(UnderTimeRequest::query()->where('employee_id', $employee_id))
            ->columns([
                TextColumn::make('under_time_id'),
                TextColumn::make('date_filling')->searchable(),
                TextColumn::make('type'),
                ColumnGroup::make('Date & Time Out', [
                    TextColumn::make('date'),
                    TextColumn::make('time_out')
                    ->getStateUsing(function ($record) {
                        return $record->time_out ? Carbon::parse($record->time_out)->format('h:i A') : '00:00';
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
             
                    return $state;
                }),
                TextColumn::make('approver_id')
                ->label('Approver')
                ->getStateUsing(function (UnderTimeRequest $record): string {

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
            ->defaultPaginationPageOption(5)
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
                ->label('Request Over Time')
                ->model(UnderTimeRequest::class)
                ->form([
                    self::underTimeForm($employee_id)
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->form([
                    self::underTimeForm($employee_id)
                ])
                ->visible(fn (UnderTimeRequest $record) => self::isActionAvailable($record)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Void')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(function (UnderTimeRequest $record, array $data) {
                    
                    $record['status'] = 'void';
                    $record->save();
                })->requiresConfirmation()
                ->visible(fn (UnderTimeRequest $record) => self::isActionAvailable($record))
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
            ])->checkIfRecordIsSelectableUsing(fn (UnderTimeRequest $record) => self::isActionAvailable($record));
    }

    public static function underTimeForm($employee_id): Grid
    {
        return Grid::make([
            'default' => 1
        ])
        ->schema([
            DatePicker::make('date_filling')
            ->label('Date Filled')
            ->default(function () {
                return Carbon::now()->format('Y-m-d');
            })
            ->readOnly()
            ->suffixIcon('heroicon-o-calendar-days'),
            Section::make('Date & Time')
            ->description('')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                DatePicker::make('date')->required()
                ->live()
                ->label('Date')
                ->suffixIcon('heroicon-o-calendar-days'),
                TimePicker::make('time_out')->required()
                ->live()
                ->seconds(false)
                ->label('Time out'),
            ])->columns(1),
            Select::make('type')->options([
                '1' => 'Emergency',
                '2' => 'Sick',
            ]),
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

    public static function isActionAvailable(UnderTimeRequest $record): bool {

        if ($record->status == "void") {
            return false;
        }
    
        return true;
    }

    public function render(): View
    {
        return view('livewire.under-time-request-table');
    }
}
