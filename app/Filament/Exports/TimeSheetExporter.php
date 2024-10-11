<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use App\Models\TimeLog;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Filament\Actions\Exports\Models\Export;

class TimeSheetExporter implements ToCollection
{
    public function __construct()
    {
        // $this->raffle_id = $raffle_id;
    }


    public function collection(Collection $rows)
    {
        return $rows->map(function($row) {

                dump($row);
                die;
                $biometric_id = $row[1];
                $type = "";
                $date = "";
                $employee = Employee::where('biometric_id', $biometric_id)->first();
                $location = "";
                $time = "";

                if($employee){
                    $result = $employee->employee_timelogs()->create([
                        'date' => $date,
                        'day' => now()->format('l'),
                        'type' => $type,
                        'time' => $time,
                        'location' => $location
                    ]);

                    $timesheet = isset($employee) ? $employee->employee_timesheets()->where('date', $date)->first() : "";

                    if ($timesheet) {
                                    
                        ($result->type == "TIMEIN") ? $timesheet->time_in : $timesheet->time_out = $result->time;
                        $timesheet->in_location = $location;
                        $timesheet->save();
                    } else {

                        // if no timesheet create for this attendance
                        $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
                        $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
                        $schedule = $time_in . ' - ' . $time_out;

                        $employee->employee_timesheets()->create([
                            'date' => $result->date,
                            'shift_schedule' => $schedule,
                            ($result->type == "TIMEIN") ? 'time_in' : 'time_out' => $result->time,
                            'in_location' => $location,
                        ]);
                    }
                }
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
