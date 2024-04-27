<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Select;
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
                Select::make('roles')->multiple()->relationship('roles', 'name')
            ]);
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
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()
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
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
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
