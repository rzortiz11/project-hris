<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use App\Models\Employee;
use App\Models\Holiday;
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
            'selectable' => false,
            'editable' => false,
        ];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $today = Carbon::today();
            
        // Get the list of holidays
        $holidays = Holiday::getPhilippineHolidays();
    
        // Add holidays to the events list
        $holidayEvents = $holidays->map(function ($holiday) {
            return [
                'id' => null,
                'title' => $holiday['name'].' - '. $holiday['type'],
                'start' => Carbon::parse($holiday['date'])->startOfDay()->format('Y-m-d H:i:s'),
                'end' => Carbon::parse($holiday['date'])->endOfDay()->format('Y-m-d H:i:s'),
                'backgroundColor' => 'rgb(255, 182, 193)',
                'holiday' => true,
            ];
        })->all();
    
        $events = Leave::query()
            ->where(function ($query) use ($today) {
                $query->where('from', '<=', $today) // Event starts on or before today
                      ->where('to', '>=', $today); // Event ends today or later
            })
            ->orWhere('from', '>', $today) // Event starts in the future
            // ->where(function ($query) {
            //     $query->where('status', '!=', 'CANCELLED')
            //           ->orWhereNull('status');
            // })
            ->where('status', 'approved')
            ->get()
            ->map(function (Leave $event) use ($today) {
                // Adjust start date if it's in the past
                $start = Carbon::parse($event->from);
                if ($start->lt($today)) {
                    $start = $today->copy()->startOfDay();
                }
    
                // Retrieve employee details
                $employee = Employee::find($event->employee_id);
                $employee_name = $employee ? ucwords(strtolower($employee->user->name)) : '';
                $designation = isset($employee->position->reporting_designation) ? $employee->position->reporting_designation : "N/A";
                $title = $employee_name . ' - ' . $designation . ' : ';
                $title .=  $start->isSameDay($today) ? ' on '.$event->type.' today' : $event->type;
            
                return [
                    'id' => $event->leave_id,
                    'title' => $title,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => Carbon::parse($event->to)->endOfDay()->format('Y-m-d H:i:s'),
                    'holiday' => false,
                ];
            })
            ->all();

        // Merge the events and holidayEvents
        $array =  array_merge($holidayEvents, $events);

        return $array;
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
                if (event.extendedProps.holiday) {
                    el.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                    });
                }
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
