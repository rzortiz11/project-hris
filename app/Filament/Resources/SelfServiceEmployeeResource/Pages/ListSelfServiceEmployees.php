<?php

namespace App\Filament\Resources\SelfServiceEmployeeResource\Pages;

use App\Filament\Resources\SelfServiceEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSelfServiceEmployees extends ListRecords
{
    protected static string $resource = SelfServiceEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
