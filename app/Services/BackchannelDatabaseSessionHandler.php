<?php

namespace App\Services;

use App\Domain\Iam\Services\BackchannelLogoutService;
use App\Models\User;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\Log;

class BackchannelDatabaseSessionHandler extends DatabaseSessionHandler
{
    protected BackchannelLogoutService $logoutService;

    public function __construct(
        ConnectionInterface $connection,
        string $table,
        int $minutes,
        BackchannelLogoutService $logoutService,
        ?Container $container = null
    ) {
        parent::__construct($connection, $table, $minutes, $container);

        $this->logoutService = $logoutService;
    }

    public function destroy($sessionId): bool
    {
        $session = $this->getQuery()->find($sessionId);

        if ($session) {
            Log::info('session.handler.destroy', [
                'session_id' => $sessionId,
                'user_id' => $session->user_id,
            ]);
        } else {
            Log::info('session.handler.destroy.not_found', [
                'session_id' => $sessionId,
            ]);
        }

        $this->notifyLogoutForSession($session);

        return parent::destroy($sessionId);
    }

    public function gc($lifetime): int
    {
        $expiredAt = $this->currentTime() - $lifetime;

        $userIds = $this->getQuery()
            ->where('last_activity', '<=', $expiredAt)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        Log::info('session.handler.gc', [
            'lifetime' => $lifetime,
            'expired_at' => $expiredAt,
            'user_ids' => $userIds,
        ]);

        if (! empty($userIds)) {
            User::whereIn('id', $userIds)
                ->get()
                ->each(function (User $user) {
                    Log::info('session.handler.gc.notify_user', [
                        'user_id' => $user->id,
                        'user_nip' => $user->nip,
                        'user_email' => $user->email,
                    ]);

                    $this->logoutService->notifyUser($user, [], true);
                });
        }

        return parent::gc($lifetime);
    }

    protected function notifyLogoutForSession(?\stdClass $session): void
    {
        if (! $session || empty($session->user_id)) {
            return;
        }

        $user = User::find($session->user_id);
        if (! $user) {
            return;
        }

        $this->logoutService->notifyUser($user, [], true);
    }
}
