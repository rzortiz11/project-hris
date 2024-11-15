<?php

namespace Database\Seeders;

use App\Models\UtilityBarangay;
use App\Models\UtilityDisctrict;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityBarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $district_code = [];
        $districts = UtilityDisctrict::all();
        if ( $districts->count() > 0 ) {
            foreach($districts as $district) {
                $district_code[$district->code] = $district->utility_district_id;
            }
        }

        if ( !empty($district_code) ) {
            $file_path = base_path('data/barangay.csv');
            $file = fopen( $file_path , 'r');

            // Skip the first line
            fgets($file);

            UtilityBarangay::truncate();

            $i = 0;
            while ( !feof($file) ) {
                if ( $i > 0 ) {
                    $data = fgetcsv($file);
                    if ( is_array($data) ) {
                        if ( count($data) === 4 ) {
                            $dist_code = $data[0];
                            $brgy_code = $data[2];
                            $brgy_name = $data[3];

                            if ( isset($district_code[$dist_code])) {
                                $district_id = $district_code[$dist_code];
                                UtilityBarangay::create([
                                    'utility_district_id' => $district_id,
                                    'name' => $brgy_name,
                                    'code' => $brgy_code,
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
