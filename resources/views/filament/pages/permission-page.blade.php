<x-filament-panels::page>
      {{ $this->form }}

    @if ($this->data['role'])
        <x-filament::button wire:click="save" class="w-full" size="xl" color="success"
            style="margin-top: 10px; margin-bottom: 10px;">
            Salvar
        </x-filament::button>
    @endif
    <div wire:loading.remove wire:target='data.role, data.screen'>
        {{ $this->permissions() }}
    </div>
</x-filament-panels::page>
