<?php

namespace App\Filament\Resources\AbsenceSelfServiceResource\Pages;

use App\Filament\Resources\AbsenceSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsenceSelfService extends EditRecord
{
    protected static string $resource = AbsenceSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
