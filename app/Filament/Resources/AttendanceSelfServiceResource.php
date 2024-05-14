<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages\ViewEmployeeTimeSheet;
use App\Filament\Resources\AttendanceSelfServiceResource\Pages;
use App\Filament\Resources\AttendanceSelfServiceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\AttendanceSelfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceSelfServiceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'Attendance';

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
            'view' => ViewEmployeeTimeSheet::route('/{record}/view'),
            'index' => ViewEmployeeTimeSheet::route("/timesheet/view"),
            //how the hell does this even work :))
        ];
    }
}
