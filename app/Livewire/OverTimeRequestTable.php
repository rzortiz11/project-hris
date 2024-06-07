<?php

namespace App\Livewire;

use App\Models\EmployeeLeaveApprover;
use App\Models\OverTimeRequest;
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

class OverTimeRequestTable extends Component implements HasForms, HasTable
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
            ->query(OverTimeRequest::query()->where('employee_id', $employee_id))
            ->columns([
                TextColumn::make('over_time_id'),
                TextColumn::make('date_filling')->searchable(),
                TextColumn::make('type'),
                ColumnGroup::make('From Date/Time', [
                    TextColumn::make('date_from'),
                    TextColumn::make('time_from')
                    ->getStateUsing(function ($record) {
                        return $record->time_from ? Carbon::parse($record->time_from)->format('h:i A') : '00:00';
                    }),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                ColumnGroup::make('To Date/Time', [
                   TextColumn::make('date_to'),
                   TextColumn::make('time_to')
                    ->getStateUsing(function ($record) {
                        return $record->time_to ? Carbon::parse($record->time_to)->format('h:i A') : '00:00';
                    }),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),
                TextColumn::make('hours'),
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
                ->getStateUsing(function (OverTimeRequest $record): string {

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
                ->model(OverTimeRequest::class)
                ->form([
                    self::timeChangeForm($employee_id)
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->form([
                    self::timeChangeForm($employee_id)
                ])
                ->visible(fn (OverTimeRequest $record) => self::isActionAvailable($record)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Void')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->action(function (OverTimeRequest $record, array $data) {
                    
                    $record['status'] = 'void';
                    $record->save();
                })->requiresConfirmation()
                ->visible(fn (OverTimeRequest $record) => self::isActionAvailable($record))
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
            ])->checkIfRecordIsSelectableUsing(fn (OverTimeRequest $record) => self::isActionAvailable($record));
    }

    public static function timeChangeForm($employee_id): Grid
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
            Grid::make()
            ->schema([
                Grid::make([
                    'default' => 1
                ])
                ->columnSpan(5)
                ->schema([
                    Section::make('From Date & Time')
                    ->description('')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        DatePicker::make('date_from')->required()
                        ->afterStateUpdated(function (Get $get, Set $set){
                            
                            self::calculateHours($get,$set);
                        })
                        ->live()
                        ->label('Date')
                        ->suffixIcon('heroicon-o-calendar-days'),
                        TimePicker::make('time_from')->required()
                        ->afterStateUpdated(function (Get $get, Set $set){
                            
                            self::calculateHours($get,$set);
                        })
                        ->live()
                        ->seconds(false)
                        ->label('Time'),
                    ])->columns(1),
                ]),
    
                Grid::make([
                    'default' => 1
                ])
                ->columnSpan(5)
                ->schema([
                    Section::make('To Date & Time')
                    ->description('')
                    ->icon('heroicon-s-calendar-days')
                    ->schema([
                        DatePicker::make('date_to')->required()
                        ->afterStateUpdated(function (Get $get, Set $set){
                            
                            self::calculateHours($get,$set);
                        })
                        ->live()
                        ->label('Date')
                        ->suffixIcon('heroicon-o-calendar-days'),
                        TimePicker::make('time_to')->required()
                        ->afterStateUpdated(function (Get $get, Set $set){

                            self::calculateHours($get,$set);
                        })
                        ->live()
                        ->seconds(false)
                        ->label('Time'),
                    ])->columns(1),
                ]),
                Grid::make([
                    'default' => 1
                ])
                ->columnSpan(2)
                ->schema([
                    TextInput::make('hours')->label('Overtime Hours')
                ])
                ->extraAttributes(['class' => 'bg-gray-600'])
                ->columns(1)
                ->grow(false),    
            ])
            ->columns(12),
            Select::make('type')->options([
                '1' => 'Regular',
                '2' => 'Special Holiday',
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

    public static function isActionAvailable(OverTimeRequest $record): bool {

        if ($record->status == "void") {
            return false;
        }
    
        return true;
    }

    public static function calculateHours($get,$set) {

        $date_from = $get('date_from');
        $time_from = $get('time_from');
        $date_to = $get('date_to');
        $time_to = $get('time_to');
        
        if ($date_from && $time_from && $date_to && $time_to) {
            $datetime_from = Carbon::parse("$date_from $time_from");
            $datetime_to = Carbon::parse("$date_to $time_to");
            $hours = $datetime_from->diffInHours($datetime_to);
            $set('hours', $hours);
        }
    }

    public function render(): View
    {
        return view('livewire.over-time-request-table');
    }
}
