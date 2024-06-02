<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveSelfServiceResource\Pages\EmployeeSelfServiceLeaveView;
use App\Models\Leave;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveSelfServiceResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'Leave';

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
            'view' => EmployeeSelfServiceLeaveView::route('/{record}/view'),
            'index' => EmployeeSelfServiceLeaveView::route("/leave/view"),
        ];
    }
}
