<?php

namespace App\Filament\Resources\CompanyAnnouncementResource\Pages;

use App\Filament\Resources\CompanyAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyAnnouncement extends EditRecord
{
    protected static string $resource = CompanyAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->requiresConfirmation(),
        ];
    }
}
