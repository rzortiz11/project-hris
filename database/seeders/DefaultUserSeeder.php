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
        $default = [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@morepower.ph',
            'password' => Hash::make('admin1234')
        ];

        $user = User::create($default);
        // $user->assignRole('Admin');
    }
}
