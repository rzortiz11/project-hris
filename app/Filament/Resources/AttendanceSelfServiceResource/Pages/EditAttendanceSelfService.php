<?php

namespace App\Filament\Resources\AttendanceSelfServiceResource\Pages;

use App\Filament\Resources\AttendanceSelfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceSelfService extends EditRecord
{
    protected static string $resource = AttendanceSelfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->requiresConfirmation(),
        ];
    }
}
