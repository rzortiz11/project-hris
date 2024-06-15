<?php

namespace App\Filament\Resources\NoticeBoardResource\Pages;

use App\Filament\Resources\NoticeBoardResource;
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
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
    
        if ($record->users_id) {
            $currentUserIds = NoticeEmployee::where('notice_board_id', $record->notice_board_id)
                ->pluck('user_id')
                ->toArray();
    
            $newUserIds = $record->users_id;
    
            // Users to be removed
            $usersToRemove = array_diff($currentUserIds, $newUserIds);
    
            // Remove users that are no longer associated
            if (!empty($usersToRemove)) {
                NoticeEmployee::where('notice_board_id', $record->notice_board_id)
                    ->whereIn('user_id', $usersToRemove)
                    ->delete();
            }
    
            // Users to be added
            $usersToAdd = array_diff($newUserIds, $currentUserIds);
    
            // Add new users and send notifications
            foreach ($usersToAdd as $user_id) {
                $user = User::find($user_id);
    
                if (isset($user)) {
                    NoticeEmployee::create([
                        'notice_board_id' => $record->notice_board_id,
                        'user_id' => $user->user_id,
                    ]);
    
                    self::sendNotification($user);
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
