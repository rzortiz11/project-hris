<?php

namespace App\Filament\Resources\MyPayslipResource\Pages;

use App\Filament\Resources\MyPayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyPayslip extends EditRecord
{
    protected static string $resource = MyPayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
