<?php

namespace App\Filament\Resources\EmployeeLeaveServiceResource\Widgets;

use App\Models\EmployeeLeaveBalance;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\Reactive;
class LeaveSelfServiceAllocationPieChart extends ChartWidget 
{
    protected static ?string $heading = 'Chart';

    public $leave_balance_id = "";
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;
    #[Reactive] 
    public $record;
 
    protected function getData(): array
    {
        if($this->record){
            $Leavebalance = EmployeeLeaveBalance::find($this->record);
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
