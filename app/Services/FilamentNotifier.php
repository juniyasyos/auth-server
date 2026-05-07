<?php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;

class FilamentNotifier
{
    /**
     * Send a Filament database notification to a user.
     *
     * @param int $userId
     * @param string $title
     * @param string $body
     * @param string $level success|warning|danger|info
     * @return void
     */
    public static function notifyToUser(int $userId, string $title, string $body, string $level = 'success'): void
    {
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $notification = Notification::make()
            ->title($title)
            ->body($body);

        if ($level === 'success') {
            $notification->success();
        } elseif ($level === 'warning') {
            $notification->warning();
        } elseif ($level === 'danger') {
            $notification->danger();
        } else {
            $notification->info();
        }

        $notification->sendToDatabase($user);
    }
}
