<?php

namespace App\Filament\Resources\MyPayslipResource\Pages;

use App\Filament\Resources\MyPayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyPayslips extends ListRecords
{
    protected static string $resource = MyPayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
