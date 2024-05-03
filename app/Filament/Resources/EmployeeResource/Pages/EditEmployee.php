<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->requiresConfirmation(),
        ];
    }

    protected function afterSave(): void
    {
           
        // Create a asynchronos to generate timesheet from current date to end of the month
        // when done, Notify that timesheet is created for this user. 

        $employee = $this->record;

        $time_in = "00:00";
        $time_out = "00:00";

        if($employee->employment->time_in && $employee->employment->time_out){

            $time_in = Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A');
            $time_out = Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A');
        }
        
        $schedule = $time_in . ' - ' . $time_out;
        
        $startDayOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        while ($startDayOfMonth <= $endOfMonth) {

            // Check if a timesheet already exists for this employee on this day
            if (!$employee->employee_timesheets()->where('date', $startDayOfMonth)->exists()) {
                $employee->employee_timesheets()->create([
                    'date' => $startDayOfMonth,
                    'shift_schedule' => $schedule
                ]);
            }

            $startDayOfMonth->addDay();
        }
    }
}
