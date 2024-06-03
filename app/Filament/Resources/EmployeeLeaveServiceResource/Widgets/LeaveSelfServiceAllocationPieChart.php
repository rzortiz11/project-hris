<?php

namespace App\Filament\Resources\EmployeeLeaveServiceResource\Widgets;

use App\Models\EmployeeLeaveBalance;
use Filament\Widgets\ChartWidget;

class LeaveSelfServiceAllocationPieChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    public $leave_balance_id = "";
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;
    // protected static bool $isLazy = false;

    protected $listeners = [
        'updateAllocationPieChart'
    ];

    public function updateAllocationPieChart($leave_balance_id)
    {
        $this->leave_balance_id = $leave_balance_id;
    }

    protected function getData(): array
    {
        if($this->leave_balance_id){
            $Leavebalance = EmployeeLeaveBalance::find($this->leave_balance_id);
            return [
                'datasets' => [
                    [
                        'label' => $Leavebalance['type'],
                        'data' => [0 ,$Leavebalance['used_balance'], $Leavebalance['remaining_balance']],
                        'backgroundColor' => [
                            'rgb(255, 255, 255)',
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                        ]
                    ],
                ],
                'labels' => [
                    'Allocated (' . $Leavebalance['balance'] . ')',
                    'Used Balance (' . $Leavebalance['used_balance'] . ')',
                    'Remaining Balance (' . $Leavebalance['remaining_balance'] . ')',
                ],
            ];
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
