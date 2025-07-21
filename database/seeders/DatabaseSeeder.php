<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            ApplicationStatusTypeSeeder::class,
            ApplicantSeeder::class,
            DefaultUserSeeder::class,
            UtilityRegionSeeder::class,
            UtilityCitySeeder::class,
            UtilityDistrictSeeder::class,
            UtilityBarangaySeeder::class,
        ]);
    }
}
