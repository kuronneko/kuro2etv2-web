<?php

namespace App\Services;

use App\Models\File2e;
use Illuminate\Support\Facades\Auth;

class File2eActionService
{
    public static function encryptOrDecrypt($data, $boolean): array
    {
        if (Auth::user()->id != $data['user_id']) {
            abort(404);
        } else {
            if ($boolean) {

                $data['text'] = File2eService::saveTextToHex($data['text']);
                return $data;
            } else if (!$boolean) {

                $data['text_encrypted'] = $data['text'];
                $data['text'] = File2eService::loadHexToString($data['text']);
                return $data;
            }
        }
    }

    public static function updateFile2e(File2e $file2e, $request)
    {
        if (Auth::user()->id != $file2e->user->id) {
            abort(404);
        } else {

            $updatedArray = array_merge($file2e->toArray(), ['text' => $request->text]);

            $file2e->update([
                'name' => $request->name,
                'text' => File2e::make(self::encryptOrDecrypt($updatedArray, true))->setAttribute('id', $file2e->id)->text
            ]);
        }
    }
}
