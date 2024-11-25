<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TimeSheetExporter;
use App\Filament\Exports\TimeSheetImporterExample;
use App\Filament\Imports\TimeSheetImporter;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\Pages\ViewEmployeeTimeSheet;
use App\Models\Employee;
use App\Models\EmployeeManagement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Actions\Action as formAction;
use Filament\Forms\Components\Actions as formComponentACtion;

class AttendanceResource extends Resource
{
    // protected static ?string $model = Attendance::class;
    // Replace new employee button as Generate Timesheet to a Employee.

    protected static ?string $model = EmployeeManagement::class;

    protected static ?string $modelLabel = "Employee Attendance";

    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    protected static ?string $navigationGroup = 'Human Resource Management';

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
            ->headerActions([
                Action::make('importer_time_logs')
                ->label('Timesheet Importer')
                ->color('primary')
                ->icon('heroicon-o-arrow-up-on-square')
                ->iconPosition(IconPosition::After)
                ->requiresConfirmation()
                ->form([
                    FileUpload::make('csv')
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain'])
                    ->required()
                    ->label('CSV File'),
                    formComponentACtion::make([
                        formAction::make('export')
                        ->label('Download example CSV file')
                        ->link()
                        ->icon('heroicon-o-arrow-down-on-square')
                        ->action(function () {
                            return Excel::download(new TimeSheetImporterExample(), 'timesheet-importer-example.csv');
                        }),
                    ]),
                ])
                ->action(function ($data) {
                    Excel::import(new TimeSheetImporter(), $data['csv'], 'public');
                }),
            ])
            ->columns([
                TextColumn::make('employee_id')->label('ID'),
                ImageColumn::make('avatar')
                ->grow(false)
                ->getStateUsing(function (Employee $data): string {

                    return isset($data->picture) ? $data->picture : '';
                })
                ->circular(),
                TextColumn::make('employee_reference')->searchable(),
                TextColumn::make('user.name')->label('User')->searchable(['first_name','last_name']),
                TextColumn::make('position.job_position')->label('Position'),
                TextColumn::make('position.reporting_designation')->label('Designation'),
                TextColumn::make('active')->badge()
                ->color(fn (string $state): string => match($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })
                ->getStateUsing(function (Employee $record): string {
                    return $record->is_active ? 'active': 'inactive';
                }),
                // TextColumn::make('created_at')->label('Created Date and Time')               
                // ->getStateUsing(function (Employee $employee): string {

                //     $created_at = Carbon::parse($employee->created_at);
                //     return $created_at->format('Y-m-d H:i:s');
                // })->searchable()       
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
