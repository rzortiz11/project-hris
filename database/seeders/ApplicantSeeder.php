<?php

namespace Database\Seeders;

use App\Models\Applicant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'first_name' => 'John', 
                'last_name' => 'Doe',
                'email' => 'johndoe@morepower.ph',
                'status' => 'New Applicant',
                'created_by' => 1,
                'assigned_to' => 1
            ],[
                'first_name' => 'Max', 
                'last_name' => 'Pain',
                'email' => 'maxpain@morepower.ph',
                'status' => 'Application for Final Inteview',
                'created_by' => 1,
                'assigned_to' => 1
            ],[
                'first_name' => 'Juan', 
                'last_name' => 'Dela Cruz',
                'email' => 'juandelacruz@morepower.ph',
                'status' => 'Application for Initial Interview',
                'created_by' => 1,
                'assigned_to' => 2
            ],
        ];

        foreach ($defaults as $row) {
            $existingRecord = Applicant::where('email', $row['email'])->first();
    
            if (!$existingRecord) {
                $user = Applicant::create($row);
                // $user->assignRole('Admin');
            }
        }
    }
}
