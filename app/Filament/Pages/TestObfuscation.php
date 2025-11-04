<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\KuroEncrypterTool;

class TestObfuscation extends Page
{
    protected static ?string $navigationLabel = 'Test Obfuscation';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.test-obfuscation';

    //position 2 in the panel
    protected static ?int $navigationSort = 2;

    // Livewire properties bound to the view
    public ?string $input = null;

    public ?string $output = null;

    public function obfuscate(): void
    {
        // show loading indicator in the button by performing a short server-side delay
        $this->output = null;
        try {
            // small artificial delay so the button loading indicator is visible
            usleep(400000); // 400ms

            $this->output = KuroEncrypterTool::saveTextToHex($this->input ?? '');
        } catch (\Throwable $e) {
            $this->output = 'Error: ' . $e->getMessage();
        }
    }

    public function deobfuscate(): void
    {
        $this->output = null;
        try {
            usleep(400000); // 400ms so the loading indicator can animate

            $this->output = KuroEncrypterTool::loadHexToString($this->input ?? '');
        } catch (\Throwable $e) {
            $this->output = 'Error: ' . $e->getMessage();
        }
    }
}
