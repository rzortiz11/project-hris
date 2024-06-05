<?php

namespace App\Livewire;
use App\Models\Employee;
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
                ->sortable()
                ->label('Status'),
            ])
            ->defaultPaginationPageOption(5)
            ->filters([
                // ...
            ])
            ->actions([
                ViewAction::make()
                ->infolist(fn(Infolist $infolist) => EmployeeLeaveHistoryTable::infolist($infolist)),
            ])
            ->bulkActions([
                // ...
            ]);
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

    public function render()
    {
        return view('livewire.employee-leave-history-table');
    }
}
