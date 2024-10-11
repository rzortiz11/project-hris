<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\TimesheetResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

class EmployeeTimeLogsPage extends ViewRecord
{
    protected ?String $heading = '';

    protected static string $resource = EmployeeResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        
        static::authorizeResourceAccess();
        $this->fillForm();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $employee = $infolist->getRecord();
        $current_date = now()->toDateString();
        $timesheet = isset($employee) ? $employee->employee_timesheets()->where('date', $current_date)->first() : "";

        return $infolist
        ->schema([
            Group::make([
                ImageEntry::make('picture')
                ->circular()
                ->size(250)
                ->label("")
                ->visibility('private')
                ->disk('public')
                ->extraAttributes([
                    'loading' => 'lazy',
                    'class' => 'justify-center'
                ]),
                TextEntry::make('user.name')->label("")
                 ->weight(FontWeight::Bold)
                 ->alignment(Alignment::Center)
                // ->color('primary')
                ->size(TextEntry\TextEntrySize::Large),
                TextEntry::make('position.job_position')->label("")
                // ->color('primary')
                ->alignment(Alignment::Center)
                ->size(TextEntry\TextEntrySize::Medium),
                Group::make([
                TextEntry::make('time_in')
                    ->alignment(Alignment::Center)
                    ->color('primary')
                    ->label('Clock In')
                    ->default('-- : --')
                    ->formatStateUsing(fn () => isset($timesheet->time_in) && $timesheet->time_in != '00:00:00'
                        ? date('h:i A', strtotime($timesheet->time_in))
                        : '-- : --'
                    ),
                
                TextEntry::make('time_out')
                    ->color('primary')
                    ->alignment(Alignment::Center)
                    ->label('Clock Out')
                    ->default('-- : --')
                    ->formatStateUsing(fn () => isset($timesheet->time_out) && $timesheet->time_out != '00:00:00'
                        ? date('h:i A', strtotime($timesheet->time_out))
                        : '-- : --'
                    ),
                ])
                ->extraAttributes(['class' => 'flex justify-center items-center text-center'])
                ->columns(2)
            ])
            ->columnSpanFull()
            ->columns(1),
 
            // Placeholder::make('Employee Name')->label('')
            // ->content(fn (Employee $record): ?string => $record ? $record->user->name : "")
            // ->extraAttributes(['class' => 'text-xs']),
            // Placeholder::make('Position')->label('')                                
            // ->content(fn (Employee $record): ?string => isset($record->position) ? $record->position->job_position : "N/A")
            // ->extraAttributes(['class' => 'text-xs']),
        ]);
    }
}
