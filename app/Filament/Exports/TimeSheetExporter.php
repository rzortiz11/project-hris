<?php

namespace App\Filament\Exports;

use App\Models\TimeSheet;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TimeSheetExporter extends Exporter
{
    protected static ?string $model = TimeSheet::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('time_sheet_id'),
            ExportColumn::make('employee_id')
            // ExportColumn::make('date'),
            // ExportColumn::make('shift_schedule'),
            // ExportColumn::make('time_in'),
            // ExportColumn::make('in_location'),
            // ExportColumn::make('in_latitude'),
            // ExportColumn::make('in_longitude'),
            // ExportColumn::make('break_time_out'),
            // ExportColumn::make('break_time_in'),
            // ExportColumn::make('time_out'),
            // ExportColumn::make('out_location'),
            // ExportColumn::make('out_latitude'),
            // ExportColumn::make('out_longitude'),
            // ExportColumn::make('out_date'),
            // ExportColumn::make('time_in_2'),
            // ExportColumn::make('time_out_2'),
            // ExportColumn::make('late_time'),
            // ExportColumn::make('over_time'),
            // ExportColumn::make('remarks'),
            // ExportColumn::make('created_at'),
            // ExportColumn::make('updated_at'),
            // ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your time sheet export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
