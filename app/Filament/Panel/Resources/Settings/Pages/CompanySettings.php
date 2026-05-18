<?php

namespace App\Filament\Panel\Resources\Settings\Pages;

use App\Filament\Panel\Resources\Settings\SettingResource;
use App\Services\SettingService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanySettings extends Page
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Company Settings';

    protected string $view = 'filament.panel.resources.settings.pages.company-settings';

    /**
     * @var array<int, array<string, mixed>>
     */
    public ?array $data = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $fields = [];

    public function mount(SettingService $settingService): void
    {
        $definitions = collect(config('settings.definitions', []))
            ->filter(fn (array $definition): bool => ($definition['group'] ?? null) === 'company');

        $currentValues = $settingService->group('company');

        $this->fields = $definitions
            ->map(function (array $definition, string $key) use ($currentValues): array {
                $stateKey = str_replace('.', '__', $key);

                $this->data[$stateKey] = $currentValues[$key] ?? $definition['default'] ?? null;

                return [
                    'key' => $key,
                    'state_key' => $stateKey,
                    'label' => $definition['description'] ?? $key,
                    'description' => $definition['description'] ?? null,
                    'input_type' => $definition['input_type'] ?? 'text',
                    'is_sensitive' => (bool) ($definition['is_sensitive'] ?? false),
                ];
            })
            ->values()
            ->all();

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        foreach ($this->fields as $field) {
            $input = $field['input_type'] === 'textarea'
                ? Textarea::make($field['state_key'])
                    ->label($field['label'])
                    ->rows(4)
                : TextInput::make($field['state_key'])
                    ->label($field['label'])
                    ->type($field['input_type'] === 'email' ? 'email' : ($field['input_type'] === 'url' ? 'url' : 'text'));

            $input->helperText('Key locked: ' . $field['key']);

            if ($field['is_sensitive']) {
                $input->password();
            }

            $components[] = Section::make(ucfirst(str_replace('.', ' ', $field['key'])))
                ->schema([
                    $input,
                ]);
        }

        return $schema
            ->statePath('data')
            ->columns(1)
            ->components($components);
    }

    public function save(SettingService $settingService): void
    {
        $state = $this->form->getState();

        foreach ($this->fields as $field) {
            $settingService->set($field['key'], $state[$field['state_key']] ?? null);
        }

        Notification::make()
            ->title('Company settings saved')
            ->body('Only setting values were updated. Keys remain locked.')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'hasFields' => ! empty($this->fields),
        ];
    }
}