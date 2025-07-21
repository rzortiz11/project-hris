<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class UserImporter implements ToCollection
{
    private $duplicates = []; // Store duplicate emails here

    public function collection(Collection $rows)
    {
        // Remove the first row (header)
        $rows->shift();

        // Process the remaining rows
        return $rows->map(function($row) {

            $email = $row[5]; // Extract the email from the current row

            if (User::where('email', $email)->exists()) {
                $this->duplicates[] = $email;
                return null; 
            }

            $result = User::create([
                'first_name'    => $row[0], 
                'last_name'    => $row[1], 
                'middle_name'    => $row[2], 
                'suffix'    => $row[3], 
                'mobile'    => $row[4],
                'email'    => $email, 
                'password'    => $row[6], 
            ]);

            if($result){
                $result->assignRole(config('constants.USER_ROLE_IS_EMPLOYEE'));

                $reference = 'MP-' . str_pad($result->user_id, 6, '0', STR_PAD_LEFT);

                

                $result->employee()->create([
                    'biometric_id' => Employee::where('biometric_id',$row[7])->exists() ? NULL : $row[7],
                    'employee_reference' => $reference,
                    'created_by' => auth()->id(),
                ]);

            }
        });
    }

    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    // create a function to notify each user of his email and password after creating a data to HRIS with url,uname,password

    public function chunkSize(): int
    {
        return 500;
    }
}
