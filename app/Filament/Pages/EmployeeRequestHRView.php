<?php

namespace App\Filament\Pages;

use App\Livewire\EmployeeLeaveRequest;
use App\Livewire\EmployeeOverTimeRequest;
use App\Livewire\EmployeeShiftChangeRequest;
use App\Livewire\EmployeeTimeChangeRequest;
use App\Livewire\EmployeeUnderTimeRequest;
use App\Models\Leave;
use App\Models\OverTimeRequest;
use App\Models\ShiftChangeRequest;
use App\Models\TimeChangeRequest;
use App\Models\UnderTimeRequest;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;

class EmployeeRequestHRView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static string $view = 'filament.pages.employee-request-h-r-view';

    protected static ?string $navigationGroup = 'Human Resource Management';

    protected static ?string $navigationLabel = 'Employee Request';

    protected ?String $heading = 'Request From Employee';

    public $record;

    public function mount()
    {
        $this->record = NULL;
    }

    public function getPendingRequestsCount($request)
    {
        if($request == "leave"){
            return Leave::query()
            ->where('status', 'pending')
            ->count();
        }

        if($request == "time_change"){
            return TimeChangeRequest::query()
            ->where('status', 'pending')
            ->count();
        }

        if($request == "over_time"){
            return OverTimeRequest::query()
            ->where('status', 'pending')
            ->count();
        }

        if($request == "under_time"){
            return UnderTimeRequest::query()
            ->where('status', 'pending')
            ->count();
        }

        if($request == "shift_change"){
            return ShiftChangeRequest::query()
            ->where('status', 'pending')
            ->count();
        }

        return 0;
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Tabs::make('Tabs')
            ->tabs([
                // Tab::make('All')
                // ->schema([
                 
                // ]),
                Tab::make('leave Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('leave') : "0";
                })
                ->icon('heroicon-o-folder-open')
                ->schema([
                    Livewire::make(EmployeeLeaveRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Time Change Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('time_change') : "0";
                })
                ->icon('heroicon-o-inbox-arrow-down')
                ->schema([
                    Livewire::make(EmployeeTimeChangeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Over Time Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('over_time') : "0";
                })
                ->icon('heroicon-o-window')
                ->schema([
                    Livewire::make(EmployeeOverTimeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Under Time Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('under_time') : "0";
                })
                ->icon('heroicon-m-arrow-uturn-down')
                ->schema([
                    Livewire::make(EmployeeUnderTimeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Shift Change Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('shift_change') : "0";
                })
                ->icon('heroicon-o-rectangle-group')
                ->schema([
                    Livewire::make(EmployeeShiftChangeRequest::class)->key(self::generateUuid())->lazy()
                ]),
            ])
            ->columnSpanFull()
            ->persistTabInQueryString()
        ])
        ->record($this->record);
    }
}
