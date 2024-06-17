<?php

namespace App\Filament\Resources\LeaveSelfServiceResource\Pages;

use App\Filament\Resources\LeaveSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveSelfService extends EditRecord
{
    protected static string $resource = LeaveSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->requiresConfirmation(),
        ];
    }
}
