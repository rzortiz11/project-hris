<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use App\Models\EmployeeLeaveBalance;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class LeaveAllocationPieChart extends ChartWidget
{
    public ?Model $record = null;

    protected static ?string $heading = 'Chart';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;
    // protected static bool $isLazy = false;
    
    public $leave_balance_id = "";

    protected function getFilters(): ?array
    {
        $employee_leaves = EmployeeLeaveBalance::where('employee_id',$this->record->employee_id)->get();

        $filters = [
            0 => 'Select'
        ];

        foreach($employee_leaves as $leave){

            $filters[$leave->leave_balance_id] = $leave->type;
        }

        return $filters;
    }
   
    protected function getData(): array
    {
        if($this->record){

            $leave_balance_id = $this->filter ? $this->filter : 0;

            // Fetch the employee leave balance using the employee_id
            $employee_leave = EmployeeLeaveBalance::find( $leave_balance_id);
            if($employee_leave){

                return [
                    'datasets' => [
                        [
                            'label' => $employee_leave['type'],
                            'data' => [0 ,$employee_leave['used_balance'], $employee_leave['remaining_balance']],
                            'backgroundColor' => [
                                'rgb(255, 255, 255)',
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                            ]
                        ],
                    ],
                    'labels' => [
                        'Allocated (' . $employee_leave->balance . ')',
                        'Used Balance (' . $employee_leave->used_balance . ')',
                        'Remaining Balance (' . $employee_leave->remaining_balance . ')',
                    ],
                ];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leave Allocation',
                    'data' => [0, 0, 0],
                ],
            ],
            'labels' => ['Allocated', 'Used Balance', 'Remaining Balance'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
