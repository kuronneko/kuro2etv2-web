<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        <x-filament::card>
            <h2 class="text-lg font-medium">Test Obfuscation</h2>
            <p class="text-sm text-gray-600">Enter text (left) and press the action to see the result (right). This page only tests obfuscation/deobfuscation — it does not save anything.</p>

            <div class="mt-4">
                <form wire:submit.prevent>
                    {{ $this->form }}

                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex gap-2">
                            <x-filament::button wire:click="obfuscate" size="sm">Obfuscate</x-filament::button>
                            <x-filament::button wire:click="deobfuscate" color="gray" size="sm">Deobfuscate (hex → text)</x-filament::button>
                            <x-filament::button wire:click="$set('input', null)" color="gray" size="sm" outlined>Clear</x-filament::button>
                        </div>

                        <div class="flex justify-end">
                            <x-filament::button wire:click="$set('output', null)" color="gray" size="sm" outlined>Clear output</x-filament::button>
                        </div>
                    </div>

                    <x-filament-actions::modals />
                </form>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
