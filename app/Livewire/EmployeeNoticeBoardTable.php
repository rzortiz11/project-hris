<?php

namespace App\Livewire;

use App\Models\NoticeEmployee;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EmployeeNoticeBoardTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $employee = auth()->user()->employee;

        return $table
            ->query(NoticeEmployee::query()
            ->where('employee_id', $employee->employee_id)
            ->with(['notice_board']))
            ->columns([
                TextColumn::make('notice_board.title')
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-bookmark'),
                Panel::make([
                    Stack::make([
                        TextColumn::make('notice_board.description')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state))
                        ->limit(40)
                        ->tooltip(function (TextColumn $column): ?HtmlString {
                            $state = $column->getState();

                            if (strlen(strip_tags($state)) <= $column->getCharacterLimit()) {
                                return null;
                            }

                            return new HtmlString(strip_tags($state));
                        })
                        ->label('Description')
                        ->markdown()
                        ->size(TextColumn\TextColumnSize::Small)
                        ->grow(false),
                        TextColumn::make('notice_board.attachments')
                        ->icon('heroicon-o-document-text'),
                    ])
                ])->collapsed(true)
            ])
            ->contentGrid([
                'default' => 1,
                'sm' =>1,
                'md' =>1,
                'lg' =>1,
                'xl' =>1,
                '2xl' =>1,
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('Read')
                ->icon('heroicon-o-eye')
                ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5);
    }

    public function render(): View
    {
        return view('livewire.employee-notice-board-table');
    }
}
