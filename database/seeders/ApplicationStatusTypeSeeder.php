<?php

namespace Database\Seeders;

use App\Models\ApplicationStatusType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationStatusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'New Applicant',
            'Application Under Review',
            'Application for Initial Interview',
            'Application for Final Inteview',
            'Application Withdrawn',
            'Background Check',
            'Offer Extended',
            'Offer Accepted',
            'Offer Declined'
        ];

        foreach ($defaults as $row) {
            $existingRecord = ApplicationStatusType::where('name', $row)->first();
    
            if (!$existingRecord) {
                ApplicationStatusType::create(['name' => $row]);
            }
        }
    }
}
