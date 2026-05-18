<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;

class SettingRepository implements SettingRepositoryInterface
{
    public function all(): Collection
    {
        return Setting::query()
            ->orderBy('category')
            ->orderBy('key')
            ->get();
    }

    public function grouped(?string $group = null): Collection
    {
        $query = Setting::query()->orderBy('category')->orderBy('key');

        if ($group !== null) {
            $query->where('category', $group);
        }

        return $query->get()->groupBy('category');
    }

    public function findByKey(string $key): ?Setting
    {
        return Setting::query()->where('key', $key)->first();
    }

    public function upsert(array $attributes): Setting
    {
        $key = (string) $attributes['key'];
        $definition = config("settings.definitions.{$key}", []);
        $type = (string) ($definition['type'] ?? $attributes['type'] ?? 'string');

        $payload = [
            'category' => (string) ($attributes['category'] ?? $attributes['group'] ?? 'general'),
            'value' => $this->serializeValue($attributes['value'] ?? ($definition['default'] ?? null), $type),
        ];

        return Setting::updateOrCreate(['key' => $key], $payload);
    }

    public function deleteByKey(string $key): bool
    {
        $setting = $this->findByKey($key);

        if (! $setting) {
            return false;
        }

        return (bool) $setting->delete();
    }

    public function upsertMany(array $items): int
    {
        $count = 0;

        foreach ($items as $key => $item) {
            if (! is_array($item)) {
                continue;
            }

            if (empty($item['key'])) {
                if (! is_string($key) && ! is_int($key)) {
                    continue;
                }

                $item['key'] = (string) $key;
            }

            if ($item['key'] === '') {
                continue;
            }

            if (! array_key_exists('value', $item)) {
                $item['value'] = $item['default'] ?? null;
            }

            if (! isset($item['category']) && isset($item['group'])) {
                $item['category'] = $item['group'];
            }

            $this->upsert($item);
            $count++;
        }

        return $count;
    }

    private function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'integer' => (string) (int) $value,
            'boolean' => $value ? 'true' : 'false',
            'array', 'json' => json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]',
            default => (is_scalar($value) || $value === null)
                ? (string) $value
                : (json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: ''),
        };
    }
}
