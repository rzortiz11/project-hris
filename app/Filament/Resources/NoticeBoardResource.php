<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeBoardResource\Pages;
use App\Models\NoticeBoard;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class NoticeBoardResource extends Resource
{
    protected static ?string $model = NoticeBoard::class;

    protected static ?string $modelLabel = "Notice Board";

    protected static ?string $navigationIcon = 'heroicon-s-bell-alert';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Notice Board';

    public static function form(Form $form): Form
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })->get();

        return $form
        ->schema([
            Section::make("Send a Notice to Employee's")
            ->schema([
                Split::make([
                    Grid::make([])
                    ->schema([
                        TextInput::make('title')->required(),
                        RichEditor::make('description')->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ]),
                        DatePicker::make('publish_at')->label('Publish Date')->nullable(),
                        Checkbox::make('visible'),
                    ]),
                    Grid::make([])
                    ->schema([
                        Select::make('users_id')->label("Select Employee's")
                        ->placeholder("Employee List")
                        ->required()
                        ->multiple()
                        ->searchable()
                        ->options($users
                        ->pluck('name', 'user_id')
                        ->map(function ($name) {
                            return ucwords(strtolower($name));
                        })
                        ->toArray()),
                        FileUpload::make('attachments')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->visibility('private')
                        ->directory('company/announcements')
                        ->multiple(),
                    ])
                ])->from('lg'),
            ])
           
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('notice_board_id')->label('ID'),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('publish_at')->label('Publish Date')
                ->getStateUsing(function (NoticeBoard $alert): string {

                    $publish_at = Carbon::parse($alert->publish_at);
                    return $publish_at->format('Y-m-d');
                }),
                ToggleColumn::make('visible'),
                TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (NoticeBoard $record): string {
                    return $record->active ? 'active': 'inactive';
                }),
                ImageColumn::make('attachments')
                ->square()
                ->stacked(),
                TextColumn::make('created_by')        
                ->getStateUsing(function (NoticeBoard $record): string {

                    $user = User::find($record->created_by);
                    return $user ? ucwords(strtolower($user->name)) : '';
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNoticeBoards::route('/'),
            'create' => Pages\CreateNoticeBoard::route('/create'),
            'edit' => Pages\EditNoticeBoard::route('/{record}/edit'),
        ];
    }
}
