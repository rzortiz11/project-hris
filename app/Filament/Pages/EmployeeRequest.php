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
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;

class EmployeeRequest extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Employee Self Service';

    protected static ?string $navigationLabel = 'Employee Request';

    protected static string $view = 'filament.pages.employee-request';

    protected ?String $heading = 'Request';

    
    public $record;

    public function mount()
    {
        $employee = auth()->user()->employee;
        $record = $employee;
        $this->record = $record;
    }

    public function getPendingRequestsCount($request, $approverId)
    {
        if($request == "leave"){
            return Leave::query()
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->count();
        }

        if($request == "time_change"){
            return TimeChangeRequest::query()
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->count();
        }

        if($request == "over_time"){
            return OverTimeRequest::query()
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->count();
        }

        if($request == "under_time"){
            return UnderTimeRequest::query()
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->count();
        }

        if($request == "shift_change"){
            return ShiftChangeRequest::query()
            ->where('approver_id', $approverId)
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
                    return $record ? $this->getPendingRequestsCount('leave',$record->user_id) : "0";
                })
                ->icon('heroicon-o-folder-open')
                ->schema([
                    Livewire::make(EmployeeLeaveRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Time Change Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('time_change',$record->user_id) : "0";
                })
                ->icon('heroicon-o-inbox-arrow-down')
                ->schema([
                    Livewire::make(EmployeeTimeChangeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Over Time Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('over_time',$record->user_id) : "0";
                })
                ->icon('heroicon-o-window')
                ->schema([
                    Livewire::make(EmployeeOverTimeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Under Time Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('under_time',$record->user_id) : "0";
                })
                ->icon('heroicon-m-arrow-uturn-down')
                ->schema([
                    Livewire::make(EmployeeUnderTimeRequest::class)->key(self::generateUuid())->lazy()
                ]),
                Tab::make('Shift Change Request')
                ->badge(function ($record) {
                    return $record ? $this->getPendingRequestsCount('shift_change',$record->user_id) : "0";
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
