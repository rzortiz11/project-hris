<?php

namespace App\Filament\Resources\PayrollAuditingResource\Pages;

use App\Filament\Resources\PayrollAuditingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollAuditing extends EditRecord
{
    protected static string $resource = PayrollAuditingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
