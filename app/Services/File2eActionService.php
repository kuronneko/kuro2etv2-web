<?php

namespace App\Services;

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
}
