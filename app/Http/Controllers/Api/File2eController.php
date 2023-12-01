<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\File2eResource;
use App\Models\File2e;
use App\Services\File2eActionService;
use App\Services\File2eService;
use Illuminate\Http\Request;

class File2eController extends Controller
{
    public function getAllByUser()
    {
        try {
            $file2es = File2e::where('user_id', auth()->user()->id)
            ->get()
            ->map(function ($file2e) {
                return request('text') == 'decrypt'
                    ? File2e::make(File2eActionService::encryptOrDecrypt($file2e->toArray(), false))->setAttribute('id', $file2e->id)
                    : $file2e;
            });
            return response()->success(
                data: File2eResource::collection($file2es),
                developerMessage: 'Registros recuperados.',
                userMessage: 'Tickets obtenidos exitosamente.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Hubo un problema al obtener los tickets. Inténtalo más tarde.'
            );
        }
    }
}
