<?php

namespace App\Services;

use App\Models\File2e;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class File2eActionService
{
    public static function encryptOrDecrypt($data, $boolean): array
    {
        if (Auth::user()->id != $data['user_id']) {
            abort(404);
        } else {
            if ($boolean) {

                // First apply the legacy obfuscation, then encrypt the result with Laravel Crypt.
                // This preserves legacy format while providing authenticated encryption.
                $hex = KuroEncrypterTool::saveTextToHex($data['text']);

                $data['text'] = Crypt::encryptString($hex);

                return $data;
            } else {
                // Try to decrypt using Laravel Crypt. If successful, the decrypted
                // value should be the legacy hex string that KuroEncrypterTool can
                // convert back to plain text. If Crypt fails, assume the value is
                // still in legacy format and use the legacy loader.
                try {
                    $hex = Crypt::decryptString($data['text']);

                    $data['obfuscated_text'] = $hex;

                    $data['text'] = KuroEncrypterTool::loadHexToString($hex);
                } catch (DecryptException $ex) {
                    // Fallback: legacy stored value (not yet Crypt-wrapped)
                    $data['text'] = KuroEncrypterTool::loadHexToString($data['text']);
                }

                return $data;
            }
        }
    }

    public static function updateFile2e(File2e $file2e, $request)
    {
        if (Auth::user()->id != $file2e->user->id) {
            abort(404);
        } else {

            // Apply legacy obfuscation then wrap with Laravel Crypt for storage
            $hex = KuroEncrypterTool::saveTextToHex($request->text);
            $file2e->update([
                'name' => $request->name,
                'text' => Crypt::encryptString($hex),
            ]);
        }
    }

    public static function storeFile2e($request)
    {
        // Apply legacy obfuscation then wrap with Laravel Crypt for storage
        $hex = KuroEncrypterTool::saveTextToHex($request->text);

        File2e::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'text' => Crypt::encryptString($hex),
        ]);
    }
}
