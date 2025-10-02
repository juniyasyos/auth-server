<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'app_key',
        'name',
        'description',
        'enabled',
        'redirect_uris',
        'logo_url',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'redirect_uris' => 'array',
        'enabled' => 'boolean',
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
}
