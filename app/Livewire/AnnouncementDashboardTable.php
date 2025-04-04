<?php

namespace App\Livewire;

use App\Models\Announcement;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AnnouncementDashboardTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Announcement::query()
            ->where('visible', true))
            ->columns([
                Split::make([
                    ImageColumn::make('attachments')
                    ->getStateUsing(function ($record) {
                        return $record->attachments ? $record->attachments : asset('/images/logo.png');
                    })
                    ->width(300)
                    ->height(250)
                    // ->size(300)
                    ->limit(1)
                    ->square()
                    ->stacked()
                    ->grow(false),
                    Stack::make([
                        TextColumn::make('title')
                        ->icon('heroicon-s-book-open')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                        TextColumn::make('description')
                        ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state))
                        ->limit(500)
                        ->markdown()
                        ->size(TextColumn\TextColumnSize::Small)
                        ->extraAttributes([
                            'style' => 'margin-top: 20px;'
                        ]),
                        Split::make([
                            ImageColumn::make('avatar')
                            ->grow(false)
                            ->getStateUsing(function (Announcement $record): string {
                                return isset($record->user->employee->pictur) ? $record->user->employee->picture : "";
                            })
                            ->circular(),
                            TextColumn::make('created_by')     
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->getStateUsing(function (Announcement $record): string {

                                return $record ? ucwords(strtolower($record->user->name)) : '';
                            })
                            ->grow(false),
                            TextColumn::make('publish_at')
                            ->grow(false)
                            ->formatStateUsing(function ($state) {
                                return ':';
                            }),
                            TextColumn::make('publish_at')   
                            ->grow(false)
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->getStateUsing(function (Announcement $record): string {

                                $publish_at = Carbon::parse($record->publish_at);
                                return $publish_at->format('Y-m-d');
                            })  
                        ])
                        ->extraAttributes([
                            'style' => 'margin-top: 20px;'
                        ])
                    ])
                    ->extraAttributes(['class' => 'flex flex-col justify-start items-start'])
                    ->space(2)
                    ->alignment(Alignment::Start)
                    ->grow(false),
                ])->from('lg'),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' =>1,
                'md' =>1,
                'lg' =>1,
                'xl' =>1,
                '2xl' =>1,
            ])
            ->defaultPaginationPageOption(5)
            ->filters([

            ])
            ->actions([
                    Tables\Actions\ViewAction::make()
                    ->label('Read')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->form([
            
                        TextInput::make('title'),
                        RichEditor::make('description'),
                        TextInput::make('publish_at')
                        ->formatStateUsing(function ($state) {
                            $publish_at = Carbon::parse($state);
                            return $publish_at->format('Y-m-d H:i');
                        }),
                        FileUpload::make('attachments')
                        ->downloadable()
                        ->openable()
                        ->deletable(false)
                        ->disk('public')
                        ->visibility('private')
                        ->directory('company/notice-board')
                        ->multiple(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.announcement-dashboard-table');
    }
}
