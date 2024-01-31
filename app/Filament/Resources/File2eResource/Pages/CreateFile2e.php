<?php

namespace App\Filament\Resources\File2eResource\Pages;

use Filament\Actions;
use App\Services\KuroEncrypterTool;
use App\Services\File2eActionService;
use App\Filament\Resources\File2eResource;
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
        return File2eActionService::encryptOrDecrypt($data, true);
    }
}
