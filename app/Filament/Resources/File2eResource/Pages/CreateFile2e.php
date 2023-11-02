<?php

namespace App\Filament\Resources\File2eResource\Pages;

use App\Filament\Resources\File2eResource;
use App\Services\File2eService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFile2e extends CreateRecord
{
    protected static string $resource = File2eResource::class;

    protected static ?string $title = 'Create File';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['text'] = File2eService::saveTextToHex($data['text']);

        return $data;
    }
}
