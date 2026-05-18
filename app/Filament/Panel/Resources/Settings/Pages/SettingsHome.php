<?php

namespace App\Filament\Panel\Resources\Settings\Pages;

use App\Filament\Panel\Resources\Settings\SettingResource;
use Filament\Resources\Pages\Page;

class SettingsHome extends Page
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Settings Center';

    protected string $view = 'filament.panel.resources.settings.pages.settings-home';

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getCards(): array
    {
        return [
            'company' => [
                'title' => 'Company Identity',
                'description' => 'Kelola nama, logo, alamat, kontak, dan profil resmi aplikasi.',
                'badge' => 'Company',
                'short' => 'CI',
                'color' => '#0ea5e9',
                'url' => SettingResource::getUrl('company'),
            ],
            'sso' => [
                'title' => 'SSO',
                'description' => 'Atur issuer, secret, TTL token, dan header verifikasi SSO.',
                'badge' => 'SSO',
                'short' => 'SS',
                'color' => '#14b8a6',
                'url' => SettingResource::getUrl('groups', ['activeTab' => 'sso']),
            ],
            'iam' => [
                'title' => 'IAM',
                'description' => 'Kelola issuer, token TTL, behavior delete, dan konfigurasi signing.',
                'badge' => 'IAM',
                'short' => 'IA',
                'color' => '#f59e0b',
                'url' => SettingResource::getUrl('groups', ['activeTab' => 'iam']),
            ],
            'auth' => [
                'title' => 'Authentication',
                'description' => 'Atur perilaku autentikasi dasar dan guard yang dipakai aplikasi.',
                'badge' => 'Auth',
                'short' => 'AU',
                'color' => '#ef4444',
                'url' => SettingResource::getUrl('groups', ['activeTab' => 'auth']),
            ],
            'fortify' => [
                'title' => 'Fortify',
                'description' => 'Sesuaikan login, username field, dan perilaku Fortify lainnya.',
                'badge' => 'Fortify',
                'short' => 'FO',
                'color' => '#22c55e',
                'url' => SettingResource::getUrl('groups', ['activeTab' => 'fortify']),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'cards' => $this->getCards(),
        ];
    }
}