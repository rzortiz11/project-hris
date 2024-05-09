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
        // TO DISABLED THIS IN THE FUTURE AND LET CRON HANDLE THE CREATION OF TIMESHEET OR WHEN TIME IN - 
        $employee = $this->record;

        $time_in = $employee->employment->time_in ? Carbon::createFromFormat('H:i:s', $employee->employment->time_in)->format('h:i A') : "00:00";
        $time_out = $employee->employment->time_out ? Carbon::createFromFormat('H:i:s', $employee->employment->time_out)->format('h:i A') : "00:00";
        $schedule = $time_in . ' - ' . $time_out;
        
        $currentDate = Carbon::now();
        $startDayOfMonth = Carbon::now()->startOfMonth();

        while ($startDayOfMonth <= $currentDate) {

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
