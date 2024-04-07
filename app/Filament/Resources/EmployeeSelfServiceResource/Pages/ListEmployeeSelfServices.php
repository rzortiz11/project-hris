<?php

namespace App\Filament\Resources\EmployeeSelfServiceResource\Pages;

use App\Filament\Resources\EmployeeSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeSelfServices extends ListRecords
{
    protected static string $resource = EmployeeSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
