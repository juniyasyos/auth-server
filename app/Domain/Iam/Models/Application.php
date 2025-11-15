<?php

namespace App\Domain\Iam\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'applications';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'app_key',
        'name',
        'description',
        'enabled',
        'redirect_uris',
        'callback_url',
        'secret',
        'logo_url',
        'token_expiry',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'redirect_uris' => 'array',
        'enabled' => 'boolean',
        'token_expiry' => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'secret',
    ];

    /**
     * Scope only enabled applications.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Find an application by its key or throw.
     */
    public static function findByKey(string $key): self
    {
        return static::where('app_key', Str::slug($key, '.'))->firstOrFail();
    }

    /**
     * Verify application secret.
     */
    public function verifySecret(string $secret): bool
    {
        return hash_equals($this->secret, hash('sha256', $secret));
    }

    /**
     * Hash and set the application secret.
     */
    public function setSecretAttribute(?string $value): void
    {
        if ($value !== null) {
            $this->attributes['secret'] = hash('sha256', $value);
        } else {
            $this->attributes['secret'] = null;
        }
    }

    /**
     * Get the token expiry in seconds (default: 3600).
     */
    public function getTokenExpirySeconds(): int
    {
        return $this->token_expiry ?? 3600;
    }

    /**
     * Check if a redirect URI is valid for this application.
     */
    public function isValidRedirectUri(string $uri): bool
    {
        if (empty($this->redirect_uris)) {
            return false;
        }

        return in_array($uri, $this->redirect_uris, true);
    }

    /**
     * Get the creator of this application.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get all roles defined for this application.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(ApplicationRole::class);
    }

    /**
     * Get only system roles for this application.
     */
    public function systemRoles(): HasMany
    {
        return $this->roles()->where('is_system', true);
    }
}
