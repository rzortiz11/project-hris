<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyPayslipResource\Pages\ViewMyPayslip;
use App\Models\EmployeeSelfService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MyPayslipResource extends Resource
{
    protected static ?string $model = EmployeeSelfService::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'My Payslip';

    protected static ?string $modelLabel = "My Payslip";

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
            'view' => ViewMyPayslip::route('/{record}/view'),
            'index' => ViewMyPayslip::route("/payslip/view"),
        ];
    }
}
