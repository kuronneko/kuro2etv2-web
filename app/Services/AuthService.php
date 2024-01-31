<?php

namespace App\Services;

use App\Mail\SolicitudVerificacionRecibida;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AuthService
{
    public static function standarLogin(string $email, string $password): User
    {
        $usuario = User::where('email', $email)->first();

        if ( ! $usuario || ! Hash::check($password, $usuario->password) ) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $usuario->tokens()->delete();
        $accessToken = $usuario->createToken('access_token')->plainTextToken;
        $usuario->setAttribute('access_token', $accessToken);

        return $usuario;
    }
}
