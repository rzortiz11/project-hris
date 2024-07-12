<?php

namespace App\Filament\Imports;

use App\Models\TimeSheet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TimeSheetImporter extends Importer
{
    protected static ?string $model = TimeSheet::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('date')
                ->rules(['date']),
            ImportColumn::make('shift_schedule')
                ->rules(['max:255']),
            ImportColumn::make('time_in')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('in_location')
                ->rules(['max:255']),
            ImportColumn::make('in_latitude')
                ->rules(['max:255']),
            ImportColumn::make('in_longitude')
                ->rules(['max:255']),
            ImportColumn::make('break_time_out')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('break_time_in')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('time_out')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('out_location')
                ->rules(['max:255']),
            ImportColumn::make('out_latitude')
                ->rules(['max:255']),
            ImportColumn::make('out_longitude')
                ->rules(['max:255']),
            ImportColumn::make('out_date')
                ->rules(['date']),
            ImportColumn::make('time_in_2')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('time_out_2')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('late_time')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('over_time')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('remarks')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?TimeSheet
    {
        // return TimeSheet::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new TimeSheet();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your time sheet import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
