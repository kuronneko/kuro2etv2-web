<?php

namespace App\Filament\Resources\File2eResource\Pages;

use Filament\Actions;
use App\Services\File2eService;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\File2eResource;

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
        if(Auth::user()->id != $data['user_id']){
            abort(404);
        }

        $data['text'] = File2eService::loadHexToString($data['text']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if(Auth::user()->id != $data['user_id']){
            abort(404);
        }

        $data['text'] = File2eService::saveTextToHex($data['text']);

        return $data;
    }
}
