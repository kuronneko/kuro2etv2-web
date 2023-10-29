<?php

namespace App\Filament\Resources\File2eResource\Pages;

use App\Filament\Resources\File2eResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFile2es extends ListRecords
{
    protected static string $resource = File2eResource::class;

    protected static ?string $title = 'Files';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New File'),
        ];
    }
}
