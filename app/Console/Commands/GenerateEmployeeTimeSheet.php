<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
    protected $description = 'Generate employee time sheet';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $employees = Employee::where('is_active',true)->get();
        $count = 0;
        $endDayOfMonth = Carbon::now()->endOfMonth();

        foreach($employees as $employee){
            
            $time_in = "00:00";
            $time_out = "00:00";

            if(isset($employee->employment->time_in) && isset($employee->employment->time_out)){

                $time_in = Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A');
                $time_out = Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A');
            
                $schedule = $time_in . ' - ' . $time_out;
                $startDayOfMonth = Carbon::now()->startOfMonth();

                while ($startDayOfMonth <= $endDayOfMonth) {

                    if (!$employee->employee_timesheets()->where('date', $startDayOfMonth)->exists()) {

                        $employee->employee_timesheets()->create([
                            'date' => $startDayOfMonth,
                            'shift_schedule' => $schedule
                        ]);        
                    }
    
                    $startDayOfMonth->addDay();
                }

                $count++;
            }
        }

        $this->info("Created timesheet for {$count} employees."."\n");
        $this->info('Start of the month: ' . Carbon::now()->startOfMonth()->toDateString());
        $this->info('End of the month: ' . $endDayOfMonth->endOfMonth()->toDateString());
    }
}
