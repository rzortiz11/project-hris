<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeSelfServiceResource\Pages;
use App\Filament\Resources\EmployeeSelfServiceResource\RelationManagers;
use App\Models\EmployeeSelfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeSelfServiceResource extends Resource
{
    protected static ?string $model = EmployeeSelfService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'Employee Details';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListEmployeeSelfServices::route('/'),
            'create' => Pages\CreateEmployeeSelfService::route('/create'),
            'edit' => Pages\EditEmployeeSelfService::route('/{record}/edit'),
        ];
    }
}
