<?php

namespace App\Filament\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class EmployeeImporterExample implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([
            [
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'middle_name'   => '',
                'suffix'        => '',
                'mobile'        => '09123456789',
                'email'         => 'jdoe@email.ph',
                'password'      => '2024518',
                'biometric_id'  => '2024518',
            ],
            [
                'first_name'    => 'Jane',
                'last_name'     => 'Smith',
                'middle_name'   => 'A.',
                'suffix'        => 'Jr.',
                'mobile'        => '09876543210',
                'email'         => 'jsmith@email.ph',
                'password'      => '123456',
                'biometric_id'  => '654321',
            ],
        ]);
    }
    
    public function headings(): array
    {
        return [
            'first_name - required', 
            'last_name - required', 
            'middle_name - nullable', 
            'suffix - nullable', 
            'mobile - required/unique/mobile',
            'email - required/unique/email', 
            'password - required', 
            'biometric_id - required/unique'
        ];
    }
}
