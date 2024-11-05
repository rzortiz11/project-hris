<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\DashboardNoPolicyResource;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;

class TodayBirthdayView extends Page
{
    protected static string $resource = DashboardNoPolicyResource::class;

    protected static string $view = 'filament.resources.employee-resource.pages.today-birthday-view';

    protected ?String $heading = '';

    public $records;
    public $currentRecordIndex = 0;
    public $currentRecord = '';
    public $currentName = '';

    public function mount()
    {
        $today = Carbon::today()->format('m-d');
        // filter where today's birthday.
        // $this->records = Employee::whereRaw('DATE_FORMAT(birthdate, "%m-%d") = ?', [$today])->get();
        $this->records = Employee::all();

        if($this->records->isNotEmpty()){
            $this->records->each(function($record) {
                $record->name = $record->user->name;
            });
        }
    
        $this->currentRecord = $this->records->isNotEmpty() ? $this->records[$this->currentRecordIndex] : null;
    }
}
