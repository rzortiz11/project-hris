<?php

namespace App\Filament\Resources\PayrollAuditingResource\Pages;

use App\Filament\Resources\PayrollAuditingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\IconPosition;

class ListPayrollAuditings extends ListRecords
{
    protected static string $resource = PayrollAuditingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
