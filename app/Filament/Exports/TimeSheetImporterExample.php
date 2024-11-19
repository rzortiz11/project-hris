<?php

namespace App\Filament\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TimeSheetImporterExample implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([
            [
                'biometric_id'  => '2024518',
                'date' => '2024-08-16',
                'time_in' => '07:29:00',
                'break_time_out' => '00:00:00',
                'break_time_in' => '00:00:00',
                'time_out' => '17:00:00',
            ],
        ]);
    }
    
    public function headings(): array
    {
        return [
            'biometric_id',
            'date - Y-m-d',
            'time_in',
            'break_time_out',
            'break_time_in',
            'time_out',
        ];
    }
}