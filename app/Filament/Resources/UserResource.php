<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'User Roles and Permissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        self::createTextInput('first_name', 'First Name', [
                            'required',
                            'string',
                            'regex:/^[a-zA-Z0-9\s]*$/',
                            'max:255',
                        ]),
                        self::createTextInput('last_name', 'Last Name', [
                            'required',
                            'string',
                            'regex:/^[a-zA-Z0-9\s]*$/',
                            'max:255',
                        ]),
                        self::createTextInput('middle_name', 'Middle Name', [
                            'string',
                            'regex:/^[a-zA-Z0-9\s]*$/',
                            'max:255',
                        ]),
                        self::createTextInput('suffix', 'Suffix', [
                            'string',
                            'regex:/^[a-zA-Z0-9\s]*$/',
                            'max:255',
                        ]),
                        TextInput::make('email')
                        ->disabled()
                        ->unique(ignoreRecord: true)
                        ->rules([
                            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        ])
                        ->placeholder('morepower.ph')
                        ->required(),
                        TextInput::make('mobile')->readOnly(true)->disabled(true),
                        TextInput::make('password')
                        ->unique(ignoreRecord: true)
                        ->minValue(12)
                        ->password()
                        ->revealable()
                        ->confirmed()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->rules([
                            'regex:/[A-Z]/', // must contain at least one upper-case letter
                            'regex:/[a-z]/', // must contain at least one lower-case letter
                            'regex:/\d/',    // must contain at least one digit
                            'regex:/[@$!%*?&]/', // must contain at least one special character
                        ]),
                        TextInput::make('password_confirmation')->label('Password Confirmation')
                        ->revealable()
                        ->password(),
                    ])->columns(2),
                    Select::make('roles')
                        ->relationship('roles')
                        ->options(Role::all()->pluck('name', 'id'))
                        ->searchable()->multiple()
                ]) ->extraAttributes(['style' => 'margin: 0 auto; max-width: 45rem; justify-self: center;'])
            ]);
    }

    private static function createTextInput(string $name, string $label, array $rules): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->rules($rules)
            ->afterStateUpdated(function ($state, callable $set) use ($name) {
                $cleanedState = strip_tags($state);
                $set($name, $cleanedState);
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')->label('ID'),
                Tables\Columns\TextColumn::make('roles.name')->label('Role'),
                Tables\Columns\TextColumn::make('first_name')->label('Name')
                    ->getStateUsing(function (User $user): string {
                        return $user->first_name . ' ' . $user->last_name;
                    }),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('mobile')->label('Mobile')->searchable(),
            ])
            ->filters([
                // Filter::make('roles')
                // ->form([
                //     Select::make('role')->options(Role::all()->pluck('name', 'id'))->multiple(),
                // ])
                // ->query(function (Builder $query, array $data): Builder {
                //     if ( $data['role'] != null ) {
                //         return $query->whereHas('roles', function(Builder $query)  use ($data) {
                //             $query->whereIn('id', $data['role']);
                //         });
                //     } else {
                //         return $query;
                //     }
                // })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
