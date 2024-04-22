<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'first_name' => 'Admin', 
                'last_name' => 'User',
                'email' => 'admin@morepower.ph',
                'password' => Hash::make('admin1234')
            ],
        ];

        foreach ($defaults as $row) {
            $existingRecord = User::where('email', $row['email'])->first();
    
            if (!$existingRecord) {
                $user = User::create($row);
                $user->assignRole('Admin');
            }
        }
    }
}
