<?php

namespace App\Observers;

use App\Domain\Iam\Services\BackchannelLogoutService;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SessionObserver
{
    /**
     * Keep track of users already notified in this request to avoid duplicate logout events.
     *
     * @var array<int, bool>
     */
    protected static array $notifiedUsers = [];

    public function deleted(Session $session): void
    {
        if (! $session->user_id) {
            Log::info('session.deleted.no_user', [
                'session_id' => $session->id,
            ]);

            return;
        }

        $user = User::find($session->user_id);
        if (! $user) {
            Log::warning('session.deleted.user_not_found', [
                'session_id' => $session->id,
                'user_id' => $session->user_id,
            ]);

            return;
        }

        Log::info('session.deleted', [
            'session_id' => $session->id,
            'user_id' => $user->id,
            'user_nip' => $user->nip,
            'user_email' => $user->email,
        ]);

        if (isset(self::$notifiedUsers[$user->id])) {
            return;
        }

        self::$notifiedUsers[$user->id] = true;

        app(BackchannelLogoutService::class)->notifyUser($user, [], true);
    }
}
