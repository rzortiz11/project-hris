<?php

namespace App\Filament\Pages;

use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;

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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Tabs::make('Tabs')
            ->tabs([
                Tab::make('All')
                ->schema([
                 
                ]),
                Tab::make('leave')
                ->icon('heroicon-o-folder-open')
                ->schema([
                 
                ]),
                Tab::make('Time Change')
                ->icon('heroicon-o-inbox-arrow-down')
                ->schema([
                 
                ]),
                Tab::make('Over Time')
                ->icon('heroicon-o-window')
                ->schema([
                 
                ]),
                Tab::make('Shift Change')
                ->icon('heroicon-o-rectangle-group')
                ->schema([
                 
                ]),
            ])
        ])
        ->record($this->record);
    }

}
