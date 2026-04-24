<?php

namespace App\Observers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SessionObserver
{
    public function created(Session $session): void
    {
        $this->updateUserLoginState($session);
    }

    public function updated(Session $session): void
    {
        if (! $session->wasChanged(['user_id', 'is_active'])) {
            return;
        }

        $this->updateUserLoginState($session, $session->getOriginal());
    }

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
    }

    private function updateUserLoginState(Session $session, array $original = []): void
    {
        if (! $session->user_id) {
            return;
        }

        $user = User::find($session->user_id);
        if (! $user) {
            return;
        }

        if ($session->is_active) {
            $user->recordLastLogin();

            return;
        }

        $user->recordLastLogout();
    }
}
