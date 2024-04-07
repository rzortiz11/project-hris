<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenceSelfServiceResource\Pages;
use App\Filament\Resources\AbsenceSelfServiceResource\RelationManagers;
use App\Models\AbsenceSelfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsenceSelfServiceResource extends Resource
{
    protected static ?string $model = AbsenceSelfService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'Absences';

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
            'index' => Pages\ListAbsenceSelfServices::route('/'),
            'create' => Pages\CreateAbsenceSelfService::route('/create'),
            'edit' => Pages\EditAbsenceSelfService::route('/{record}/edit'),
        ];
    }
}
