<?php

namespace App\Filament\Resources\NoticeBoardResource\Pages;

use App\Filament\Resources\NoticeBoardResource;
use App\Models\Employee;
use App\Models\NoticeEmployee;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class EditNoticeBoard extends EditRecord
{
    protected static string $resource = NoticeBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->requiresConfirmation()
            ->after(function ($record)  {
                $record->employees_id = [];
                $record->save();
                NoticeEmployee::where('notice_board_id', $record->notice_board_id)->delete();
            }),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
    
        if ($record->employees_id) {
            $currentEmployeeIds = NoticeEmployee::where('notice_board_id', $record->notice_board_id)
                ->pluck('employee_id')
                ->toArray();
    
            $newEmployeeIds = $record->employees_id;
    
            // Employees to be removed
            $employeesToRemove = array_diff($currentEmployeeIds, $newEmployeeIds);
    
            // Remove employees that are no longer associated
            if (!empty($employeesToRemove)) {
                NoticeEmployee::where('notice_board_id', $record->notice_board_id)
                    ->whereIn('employee_id', $employeesToRemove)
                    ->delete();
            }
    
            // Employees to be added
            $employeesToAdd = array_diff($newEmployeeIds, $currentEmployeeIds);
    
            // Add new employees and send notifications
            foreach ($employeesToAdd as $employee_id) {
                $employee = Employee::find($employee_id);
    
                if (isset($employee)) {
                    NoticeEmployee::create([
                        'notice_board_id' => $record->notice_board_id,
                        'employee_id' => $employee->employee_id,
                    ]);
    
                    self::sendNotification($employee->user);
                }
            }
        }
    }


    public static function sendNotification($recipient){

        Notification::make()
            ->title('HR Notice')
            ->body('You recieved a notice from the HR management')
            ->icon('heroicon-o-bell-alert')
            ->warning()
            ->actions([
                Action::make('view')
                    ->button()
                    ->color('warning')
                    ->url(route('filament.admin.home'), shouldOpenInNewTab: true)
            ])
            ->sendToDatabase($recipient);
        
        event(new DatabaseNotificationsSent($recipient));

        Notification::make()
        ->title('HR Notice')
        ->icon('heroicon-o-bell-alert')
        ->body('You recieved a notice from the HR management')
        ->seconds(5)
        ->actions([
            Action::make('view')
                ->button()
                ->color('warning')
                ->url(route('filament.admin.home'), shouldOpenInNewTab: true)
        ])
        ->warning()
        ->broadcast($recipient);
    }
}
