<?php

namespace App\Observers;

use App\Jobs\SyncApplicationUsers;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Attributes changes that should trigger client sync.
     *
     * @var array<string>
     */
    protected array $syncAttributes = [
        'nip',
        'name',
        'email',
        'active',
    ];

    public function __construct()
    {
        // Conditionally include password field based on config
        if (config('iam.user_sync_password_field', false)) {
            $this->syncAttributes[] = 'password';
        }
    }

    public function saved(User $user): void
    {
        if (config('iam.user_sync_mode', 'pull') !== 'push') {
            return;
        }

        if (! $user->wasRecentlyCreated && ! $user->wasChanged($this->syncAttributes)) {
            return;
        }

        $originalData = [];
        foreach ($this->syncAttributes as $attr) {
            $originalData[$attr] = $user->getOriginal($attr);
        }

        Log::info('iam.user_observer_saved', [
            'user_id' => $user->id,
            'nip' => $user->nip,
            'name' => $user->name,
            'email' => $user->email,
            'active' => $user->active,
            'created' => $user->wasRecentlyCreated,
            'changed' => $user->wasChanged($this->syncAttributes) ? $user->getChanges() : [],
            'original' => $originalData,
            'timestamp' => now()->toDateTimeString(),
        ]);

        Log::info('iam.user_observer_saved_detail', [
            'event' => 'saved',
            'user_id' => $user->id,
            'was_recently_created' => $user->wasRecentlyCreated,
            'updated_attributes' => $user->wasChanged($this->syncAttributes) ? array_keys($user->getChanges()) : [],
        ]);

        $this->dispatchUserSync($user, 'saved');
    }

    public function updated(User $user): void
    {
        if (config('iam.user_sync_mode', 'pull') !== 'push') {
            return;
        }

        if (! $user->wasChanged($this->syncAttributes)) {
            return;
        }

        Log::info('iam.user_observer_updated', [
            'user_id' => $user->id,
            'nip' => $user->nip,
            'name' => $user->name,
            'email' => $user->email,
            'active' => $user->active,
            'changed_attributes' => $user->getChanges(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $this->dispatchUserSync($user, 'updated');
    }

    public function deleted(User $user): void
    {
        if (config('iam.user_sync_mode', 'pull') !== 'push') {
            return;
        }

        Log::warning('iam.user_observer_deleted', [
            'user_id' => $user->id,
            'nip' => $user->nip,
            'email' => $user->email,
            'deleted_at' => $user->deleted_at?->toDateTimeString(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $this->dispatchUserSync($user, 'deleted');
    }

    public function restored(User $user): void
    {
        if (config('iam.user_sync_mode', 'pull') !== 'push') {
            return;
        }

        Log::info('iam.user_observer_restored', [
            'user_id' => $user->id,
            'nip' => $user->nip,
            'email' => $user->email,
            'restored_at' => $user->updated_at?->toDateTimeString(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $this->dispatchUserSync($user, 'restored');
    }

    /**
     * Triggered from relationship events / role assignment operations.
     */
    public function relationshipChanged(User $user, string $note = 'related'): void
    {
        if (config('iam.user_sync_mode', 'pull') !== 'push') {
            return;
        }

        Log::info('iam.user_observer_relationship_changed', [
            'user_id' => $user->id,
            'note' => $note,
            'roles' => $user->roles?->pluck('name')->toArray() ?? [],
            'permissions' => $user->getPermissionNames()->toArray(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $this->dispatchUserSync($user, "relationship:{$note}");
    }

    protected function dispatchUserSync(User $user, string $event): void
    {
        $changed = $user->wasChanged($this->syncAttributes) ? $user->getChanges() : [];

        Log::info('iam.user_observer_trigger', [
            'user_id' => $user->id,
            'event' => $event,
            'changed_attributes' => $changed,
            'current' => $user->only(array_unique(array_merge(['id'], $this->syncAttributes))),
        ]);

        SyncApplicationUsers::dispatch([], [], [], $user->id);
    }
}
