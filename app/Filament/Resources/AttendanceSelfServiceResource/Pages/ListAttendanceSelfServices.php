<?php

namespace App\Filament\Resources\AttendanceSelfServiceResource\Pages;

use App\Filament\Resources\AttendanceSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceSelfServices extends ListRecords
{
    protected static string $resource = AttendanceSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
