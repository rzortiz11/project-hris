<?php

namespace App\Filament\Resources\PayrollAuditingResource\Pages;

use App\Filament\Resources\PayrollAuditingResource;
use App\Livewire\ViewEmployeePayrollTable;
use App\Livewire\ViewEmployeePayrollToAuditTable;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Livewire;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Str;

class ViewPayPeriodToAudit extends ViewRecord
{
    protected static string $resource = PayrollAuditingResource::class;

    
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
        $this->fillForm();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Pay Period Details')
                ->description('Cut Off Details')
                ->icon('heroicon-s-clock')
                ->schema([
                    TextEntry::make('type'),
                    TextEntry::make('start_date'),
                    TextEntry::make('end_date'),
                    TextEntry::make('cut_off_date'),
                ])->columns(4),
                InfoSection::make("Payroll Table")
                ->schema([
                    Grid::make([
                        'default' => 1
                    ])
                    ->schema([
                        Livewire::make(ViewEmployeePayrollToAuditTable::class)->data(['record' => $this->record])->key(self::generateUuid())->lazy()
                    ])
                ])
            ]);
    }

    public static function generateUuid()
    {
        return (string) Str::uuid();
    }
}
