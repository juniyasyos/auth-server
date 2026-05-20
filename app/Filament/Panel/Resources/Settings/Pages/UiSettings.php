<?php

namespace App\Filament\Panel\Resources\Settings\Pages;

use App\Filament\Panel\Resources\Settings\SettingResource;
use App\Services\SettingService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UiSettings extends Page
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'UI Settings';

    protected string $view = 'filament.panel.resources.settings.pages.ui-settings';

    public ?array $data = [];

    public function mount(SettingService $settingService): void
    {
        $this->data = [
            'login_view' => $settingService->get('login_view', 'default'),
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('UI Settings')
                    ->description('Manage UI login view selection. Only one variant can be active.')
                    ->icon('heroicon-o-computer-desktop')
                    ->schema([
                    ((function () {
                        $imgClass = '\\Alkoumi\\FilamentImageRadioButton\\Forms\\Components\\ImageRadioGroup';

                        if (class_exists($imgClass)) {
                            return $imgClass::make('login_view')
                                ->label('')
                                ->disk('public')
                                ->options([
                                    'default' => 'images/login-page/default.jpeg',
                                    'type1' => 'images/login-page/login-type-1.jpeg',
                                    'type2' => 'images/login-page/login-type-2.jpeg',
                                ])
                                ->gridColumns(2)
                                ->required();
                        }

                        return ToggleButtons::make('login_view')
                            ->label('')
                            ->options([
                                'default' => 'Default',
                                'type1' => 'Type 1',
                                'type2' => 'Type 2',
                            ])
                            ->inline()
                            ->required();
                    })()),
                    ])
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        app(SettingService::class)->set('login_view', $state['login_view'] ?? 'default');

        Notification::make()
            ->title('UI settings saved')
            ->body('Login view selection updated.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'hasFields' => true,
        ];
    }
}
