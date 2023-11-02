<?php

namespace App\Filament\Resources\File2eResource\Pages;

use Filament\Actions;
use App\Services\File2eService;
use Illuminate\Support\Facades\Auth;
use App\Services\File2eActionService;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\File2eResource;

class ViewFile2e extends ViewRecord
{
    protected static string $resource = File2eResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return File2eActionService::encryptOrDecrypt($data, false);
    }
}
