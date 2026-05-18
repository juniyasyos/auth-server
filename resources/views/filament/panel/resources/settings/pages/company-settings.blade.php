<div class="space-y-8 mt-8">
    @if ($hasFields)
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center justify-end">
                <x-filament::button type="submit" color="primary">
                    Save Company Values
                </x-filament::button>
            </div>
        </form>
    @endif
</div>