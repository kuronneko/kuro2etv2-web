<?php

namespace App\Http\Controllers\Api;

use App\Models\File2e;
use Illuminate\Http\Request;
use App\Services\KuroEncrypterTool;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\File2eActionService;
use App\Http\Resources\File2eResource;

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

    public function get(File2e $file2e)
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

    public function edit(File2e $file2e, Request $request)
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

    public function delete(File2e $file2e)
    {
        try {
            $file2e->delete();

            return response()->success(
                developerMessage: 'Deleted record.',
                userMessage: 'File deleted successfully.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Problem to delete file. Try later.'
            );
        }
    }

    public function create(Request $request)
    {
        try {
            File2eActionService::storeFile2e($request);

            return response()->success(
                developerMessage: 'created record.',
                userMessage: 'File created successfully.'
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Problem to create file. Try later.'
            );
        }
    }
}
