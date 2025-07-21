<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateEmployeeTimeSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-employee-time-sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate employee time sheet every day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Timesheet generated at '.now());
        // $employees = Employee::where('is_active',true)->get();
        // $count = 0;
        // $currentDate = Carbon::now();

        // foreach($employees as $employee){
            
        //     if(isset($employee->employment->time_in) && isset($employee->employment->time_out)){

        //         $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
        //         $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
        //         $schedule = $time_in . ' - ' . $time_out;
        //         $startDayOfMonth = Carbon::now()->startOfMonth();

        //         while ($startDayOfMonth <= $currentDate) {

        //             if (!$employee->employee_timesheets()->where('date', $startDayOfMonth)->exists()) {

        //                 $employee->employee_timesheets()->create([
        //                     'date' => $startDayOfMonth,
        //                     'shift_schedule' => $schedule
        //                 ]);        
        //             }
    
        //             $startDayOfMonth->addDay();
        //         }

        //         $count++;
        //     }
        // }

        // $this->info("Created timesheet for {$count} employees."."\n");
        // $this->info('Start of the month: ' . Carbon::now()->startOfMonth()->toDateString());
        // $this->info('End of the month: ' . $currentDate->endOfMonth()->toDateString());
    }
}
