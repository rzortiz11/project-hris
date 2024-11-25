<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use App\Models\TimeSheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TimeSheetImporter implements ToCollection
{

    public function collection(Collection $rows)
    {
        // Remove the first row (header)
        $rows->shift();
    
        // Process the remaining rows
        return $rows->map(function($row) {
            $biometric_id = $row[0]; // Extract the biometric_id from the current row
    
            $employee = Employee::where('biometric_id', $biometric_id)->first();
    
            if ($employee) {
                // Format the date to m-d-Y
                $formatted_date = Carbon::parse($row[1])->format('Y-m-d');
    
                // Check if the timesheet entry already exists
                $existingTimesheet = TimeSheet::where('employee_id', $employee->employee_id)
                    ->where('date', $formatted_date)
                    ->first();
    
                if (!$existingTimesheet) {

                    $schedule = static::generateShiftSchedule($employee);

                    // Create new TimeSheet entry
                    TimeSheet::create([
                        'employee_id'    => $employee->employee_id,
                        'shift_schedule' => $schedule,
                        'date'           => $formatted_date,
                        'time_in'        => $row[2] ?? '00:00:00',
                        'break_time_out' => $row[3] ?? '00:00:00',
                        'break_time_in'  => $row[4] ?? '00:00:00',
                        'time_out'       => $row[5] ?? '00:00:00',
                    ]);
                } else {
                    // Update the existing TimeSheet entry
                    $existingTimesheet->update([
                        'time_in'        => $row[2] ?? '00:00:00',
                        'break_time_out' => $row[3] ?? '00:00:00',
                        'break_time_in'  => $row[4] ?? '00:00:00',
                        'time_out'       => $row[5] ?? '00:00:00',
                    ]);
                }
    
                // Check for time_in and create or update TIMEIN log
                if (!empty($row[2])) {
                    $existingTimeInLog = $employee->employee_timelogs()
                        ->where('date', $formatted_date)
                        ->where('type', 'TIMEIN')
                        ->first();
    
                    if (!$existingTimeInLog) {
                        $employee->employee_timelogs()->create([
                            'date'     => $formatted_date,
                            'day'      => now()->format('l'),
                            'type'     => 'TIMEIN',
                            'time'     => $row[2] ?? '00:00:00',
                            'location' => null,
                        ]);
                    } else {
                        $existingTimeInLog->update([
                            'time' => $row[2] ?? '00:00:00',
                        ]);
                    }
                }
    
                // Check for time_out and create or update TIMEOUT log
                if (!empty($row[5])) {
                    $existingTimeOutLog = $employee->employee_timelogs()
                        ->where('date', $formatted_date)
                        ->where('type', 'TIMEOUT')
                        ->first();
    
                    if (!$existingTimeOutLog) {
                        $employee->employee_timelogs()->create([
                            'date'     => $formatted_date,
                            'day'      => now()->format('l'),
                            'type'     => 'TIMEOUT',
                            'time'     => $row[5] ?? '00:00:00',
                            'location' => null,
                        ]);
                    } else {
                        $existingTimeOutLog->update([
                            'time' => $row[5] ?? '00:00:00',
                        ]);
                    }
                }
            }
        });
    }

    private static function generateShiftSchedule($employee)
    {
        $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
        $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
        return $time_in . ' - ' . $time_out;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}