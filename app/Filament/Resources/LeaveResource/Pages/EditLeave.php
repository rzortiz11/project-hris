<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = Action::make('return')
            ->color('info')
            ->label('Return')
            ->action(function () {
                 //artisan route:list to view the filament route list
                redirect()->route('filament.admin.resources.leaves.index');
            });

            return $actions;
    }
}
