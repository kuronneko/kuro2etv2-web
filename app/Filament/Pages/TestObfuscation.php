<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Services\KuroEncrypterTool;

class TestObfuscation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Test Obfuscation';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.test-obfuscation';

    //position 2 in the panel
    protected static ?int $navigationSort = 2;

    // Livewire properties bound to the view (will be used directly by the form fields)
    public ?string $input = null;

    public ?string $output = null;



    public function mount(): void
    {
        // initialize the form (important for Filament forms)
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('input')
                    ->label('Input')
                    ->rows(10),

                Textarea::make('output')
                    ->label('Output')
                    ->rows(10)
                    ->disabled(),
            ])
            ->columns(2);
    }

    public function obfuscate(): void
    {
        $this->output = null;
        try {
            // small artificial delay so the button loading indicator is visible
            usleep(400000); // 400ms

            $this->output = KuroEncrypterTool::saveTextToHex($this->input ?? '');
            // write the output (and input) back to the form state so the Textarea shows the new value
            $this->form->fill([
                'input' => $this->input,
                'output' => $this->output,
            ]);
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
            $this->form->fill([
                'input' => $this->input,
                'output' => $this->output,
            ]);
        } catch (\Throwable $e) {
            $this->output = 'Error: ' . $e->getMessage();
        }
    }
}
