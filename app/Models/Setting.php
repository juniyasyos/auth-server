<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'category',
        'value',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public function getValue(): mixed
    {
        $definition = config("settings.definitions.{$this->key}", []);
        $type = $definition['type'] ?? 'string';

        return match ($type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($this->value, true) ?? [],
            default => (string) $this->value,
        };
    }

    public static function getByKey(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting?->getValue() ?? $default;
    }

    public static function getByGroup(string $group): array
    {
        return static::where('category', $group)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => $s->getValue()])
            ->toArray();
    }

    public static function allAsArray(): array
    {
        return static::all()
            ->mapWithKeys(fn ($s) => [$s->key => $s->getValue()])
            ->toArray();
    }
}
