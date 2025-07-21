<?php

namespace Database\Seeders;

use App\Models\UtilityCity;
use App\Models\UtilityDisctrict;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $city_code = [];
        $cities = UtilityCity::all();
        if ( $cities->count() > 0 ) {
            foreach($cities as $city) {
                $city_code[$city->code] = $city->utility_city_id;
            }
        }

        if ( !empty($city_code) ) {
            $file_path = base_path('data/district.csv');
            $file = fopen( $file_path , 'r');

            // Skip the first line
            fgets($file);

            UtilityDisctrict::truncate();

            $i = 0;
            while ( !feof($file) ) {
                if ( $i > 0 ) {
                    $data = fgetcsv($file);
                    if ( is_array($data) ) {
                        if ( count($data) === 3 ) {
                            $cy_code = $data[0];
                            $district_code = $data[1];
                            $district_name = $data[2];

                            if ( isset($city_code[$cy_code])) {
                                $city_id = $city_code[$cy_code];
                                UtilityDisctrict::create([
                                    'utility_city_id' => $city_id,
                                    'name' => $district_name,
                                    'code' => $district_code,
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
