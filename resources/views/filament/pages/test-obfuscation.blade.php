<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        <x-filament::card>
            <h2 class="text-lg font-medium">Test Obfuscation</h2>
            <p class="text-sm text-gray-600">Enter text (left) and press the action to see the result (right). This page only tests obfuscation/deobfuscation — it does not save anything.</p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-white">Input</label>
                    <textarea wire:model.lazy="input" rows="10" class="mt-1 fi-fo-textarea block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 shadow-sm outline-none ring-1 transition duration-75 placeholder:text-gray-400 focus-visible:ring-2 sm:text-sm sm:leading-6 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500"></textarea>

                    <div class="mt-3 flex gap-2">
                        <x-filament::button wire:click="obfuscate" size="sm">Obfuscate</x-filament::button>
                        <x-filament::button wire:click="deobfuscate" color="gray" size="sm">Deobfuscate (hex → text)</x-filament::button>
                        <x-filament::button wire:click="$set('input', null)" color="gray" size="sm" outlined>Clear</x-filament::button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray">Output</label>
                    <textarea readonly rows="10" class="mt-1 fi-fo-textarea block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 shadow-sm outline-none ring-1 transition duration-75 placeholder:text-gray-400 focus-visible:ring-2 sm:text-sm sm:leading-6 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500" >{{ $output }}</textarea>

                    <div class="mt-3">
                        <x-filament::button wire:click="$set('output', null)" color="gray" size="sm" outlined>Clear output</x-filament::button>
                    </div>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
