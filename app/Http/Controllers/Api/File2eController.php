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
    public function getAll()
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
                developerMessage: 'Recovered record.',
                userMessage: 'Files obtained successfully.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Problem to obtain the files. Try later.'
            );
        }
    }

    public function getById(File2e $file2e)
    {
        try {
            $file2e = request('text') == 'decrypt'
                ? File2e::make(File2eActionService::encryptOrDecrypt($file2e->toArray(), false))->setAttribute('id', $file2e->id)
                : $file2e;

            return response()->success(
                data: new File2eResource($file2e),
                developerMessage: 'Recovered record.',
                userMessage: 'Files obtained successfully.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Problem to obtain the files. Try later.'
            );
        }
    }

    public function editById(File2e $file2e, Request $request)
    {
        try {
            File2eActionService::updateFile2e($file2e, $request);

            return response()->success(
                developerMessage: 'Updated record.',
                userMessage: 'File updated successfully.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Problem to update file. Try later.'
            );
        }
    }
}
