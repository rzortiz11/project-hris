<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class OnLeaveCalendarWidget extends FullCalendarWidget
{

    public Model | string | null $model = Leave::class;

    public function config(): array
    {
        return [
            // 'firstDay' => 1,
            'displayEventTime' => false,
            'headerToolbar' => [
                'left' => 'dayGridMonth,dayGridWeek,listWeek,dayGridDay',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
        ];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Leave::query()
        // ->where('date', '>=', $fetchInfo['start'])
        // ->where(function ($query) {
        //     $query->where('status', '!=', 'CANCELLED')
        //         ->orWhereNull('status');
        // })
        ->get()
        ->map(
            fn (Leave $event) => [
                $employee = Employee::find($event->employee_id),
                $employee_name = $employee ? ucwords(strtolower($employee->user->name)) : '',
                $designation = isset($employee->position->reporting_designation) ? $employee->position->reporting_designation : "N/A",
                'id' => $event->leave_id,
                'title' => $employee_name .' - '.$designation. ' : ' . $event->type,
                'start' => Carbon::parse($event->from)->startOfDay()->format('Y-m-d H:i:s'),
                'end' => Carbon::parse($event->to)->endOfDay()->format('Y-m-d H:i:s'),
            ]
        )
        ->all();
    }

    public function getFormSchema(): array
    {
        return [
        ];
    }

    // Event tooltip on hover
    // You can add a tooltip to fully show the event title when the user hovers over the event via JavaScript on the eventDidMount method:
    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }){
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
            }
        JS;
    }

    protected function headerActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
