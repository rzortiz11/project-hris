<?php

namespace App\Filament\Resources\CompanyAnnouncementResource\Pages;

use App\Filament\Resources\CompanyAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyAnnouncements extends ListRecords
{
    protected static string $resource = CompanyAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
