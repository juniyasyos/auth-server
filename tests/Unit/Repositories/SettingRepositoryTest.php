<?php

use App\Models\Setting;
use App\Repositories\SettingRepository;

it('normalizes associative definitions into setting payloads with default values', function () {
    $repository = new class extends SettingRepository {
        public array $payloads = [];

        public function upsert(array $attributes): Setting
        {
            $this->payloads[] = $attributes;

            return new Setting($attributes);
        }
    };

    $count = $repository->upsertMany([
        'company.name' => [
            'group' => 'company',
            'type' => 'string',
            'default' => 'ACME',
        ],
    ]);

    expect($count)->toBe(1);
    expect($repository->payloads)->toHaveCount(1);
    expect($repository->payloads[0]['key'])->toBe('company.name');
    expect($repository->payloads[0]['value'])->toBe('ACME');
});
