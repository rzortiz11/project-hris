<?php

namespace Database\Seeders;

use App\Models\UtilityRegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_path = base_path('data/region.csv');
        $file = fopen( $file_path , 'r');

        // Skip the first line
        fgets($file);

        UtilityRegion::truncate();

        $i = 0;
        while ( !feof($file) ) {
            if ( $i > 0 ) {
                $data = fgetcsv($file);
                UtilityRegion::create([
                    'name' => $data[1],
                    'code' => $data[0],
                ]);
            }
            $i++;
        }

        fclose($file);
    }
}
