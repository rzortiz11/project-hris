<?php

namespace App\Filament\Resources\DashboardNoPolicyResource\Pages;

use App\Filament\Resources\DashboardNoPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDashboardNoPolicies extends ListRecords
{
    protected static string $resource = DashboardNoPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
