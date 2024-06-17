<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $modelLabel = "News & Announcement";

    protected static ?string $navigationIcon = 'heroicon-s-megaphone';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'News & Announcement';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Announcement')
                ->schema([
                    Split::make([
                        Grid::make([
                            ])->schema([
                                TextInput::make('title')->required(),
                                RichEditor::make('description')->required()
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ]),
                                DatePicker::make('publish_at')->label('Publish Date')->nullable(),
                                DatePicker::make('expires_at')->label('Expiration Date')->nullable(),
                                Checkbox::make('visible'),
                                
                            ]),
                            // image in announcement dashboard is set to 300 width and 250 height please crop the image
                            FileUpload::make('attachments')
                            ->downloadable()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->visibility('private')
                            ->directory('company/announcements')
                            ->multiple(),
                    ])->from('lg'),
                ])
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('announcement_id')->label('ID'),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('publish_at')->label('Publish Date')
                ->getStateUsing(function (Announcement $alert): string {

                    $publish_at = Carbon::parse($alert->publish_at);
                    return $publish_at->format('Y-m-d');
                }),
                ToggleColumn::make('visible'),
                TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (Announcement $record): string {
                    return $record->active ? 'active': 'inactive';
                }),
                ImageColumn::make('attachments')
                ->square()
                ->stacked(),
                TextColumn::make('created_by')        
                ->getStateUsing(function (Announcement $record): string {

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
                    Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
