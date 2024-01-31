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

                $data['text'] = KuroEncrypterTool::saveTextToHex($data['text']);
                return $data;
            } else if (!$boolean) {

                $data['text_encrypted'] = $data['text'];
                $data['text'] = KuroEncrypterTool::loadHexToString($data['text']);
                return $data;
            }
        }
    }

    public static function updateFile2e(File2e $file2e, $request)
    {
        if (Auth::user()->id != $file2e->user->id) {
            abort(404);
        } else {

            $file2e->update([
                'name' => $request->name,
                'text' => KuroEncrypterTool::saveTextToHex($request->text),
            ]);
        }
    }

    public static function storeFile2e($request)
    {
        File2e::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'text' => KuroEncrypterTool::saveTextToHex($request->text),
        ]);
    }
}
