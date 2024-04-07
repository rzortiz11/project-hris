<?php

namespace App\Filament\Resources\LeaveSelfServiceResource\Pages;

use App\Filament\Resources\LeaveSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveSelfServices extends ListRecords
{
    protected static string $resource = LeaveSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
