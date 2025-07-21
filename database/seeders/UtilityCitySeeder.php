<?php

namespace Database\Seeders;

use App\Models\UtilityCity;
use App\Models\UtilityRegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $region_code = [];
        $regions = UtilityRegion::all();
        if ( $regions->count() > 0 ) {
            foreach($regions as $region) {
                $region_code[$region->code] = $region->utility_region_id;
            }
        }
      
        if ( !empty($region_code) ) {
            $file_path = base_path('data/city.csv');
            $file = fopen( $file_path , 'r');

            // Skip the first line
            fgets($file);

            UtilityCity::truncate();

            $i = 0;
            while ( !feof($file) ) {
                if ( $i > 0 ) {
                    $data = fgetcsv($file);
                    if ( is_array($data) ) {
                        if ( count($data) === 3 ) {
                            $reg_code = $data[0];
                            $city_code = $data[1];
                            $city_name = $data[2];

                            if ( isset($region_code[$reg_code])) {
                                $region_id = $region_code[$reg_code];
                                UtilityCity::create([
                                    'utility_region_id' => $region_id,
                                    'name' => $city_name,
                                    'code' => $city_code,
                                ]);
                            }
                        }
                    }
                }
                $i++;
            }

            fclose($file);
        }
    }
}
