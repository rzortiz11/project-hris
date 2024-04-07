<?php

namespace App\Filament\Resources\AbsenceSelfServiceResource\Pages;

use App\Filament\Resources\AbsenceSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsenceSelfServices extends ListRecords
{
    protected static string $resource = AbsenceSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
