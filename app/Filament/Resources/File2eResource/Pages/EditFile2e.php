<?php

namespace App\Filament\Resources\File2eResource\Pages;

use Filament\Actions;
use App\Services\File2eService;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\File2eResource;
use App\Services\File2eActionService;

class EditFile2e extends EditRecord
{
    protected static string $resource = File2eResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return File2eActionService::encryptOrDecrypt($data, false);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return File2eActionService::encryptOrDecrypt($data, true);
    }
}
