<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\Pages\ViewEmployeeTimeSheet;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    // protected static ?string $model = Attendance::class;
    // Replace new employee button as Generate Timesheet to a Employee.

    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Attendance Management';

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
                Tables\Columns\TextColumn::make('employee_id')->label('ID'),
                Tables\Columns\TextColumn::make('employee_reference')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable(['first_name','last_name'])
                ,
                Tables\Columns\TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (Employee $record): string {
                    return $record->is_active ? 'active': 'inactive';
                }),
                // Tables\Columns\TextColumn::make('created_at')->label('Created Date and Time')               
                // ->getStateUsing(function (Employee $employee): string {

                //     $created_at = Carbon::parse($employee->created_at);
                //     return $created_at->format('Y-m-d H:i:s');
                // })->searchable()       
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
           
            ]);
            // ->poll('10s');
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
            'index' => Pages\ListAttendances::route('/'),
            'view' => ViewEmployeeTimeSheet::route('/{record}/timesheet'),
        ];
    }
}
