<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\LoginResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\Auth\StandarLoginRequest;

class AuthController extends Controller
{
    public function standarLogin(StandarLoginRequest $request): JsonResponse
    {
        try {
            $usuario = AuthService::standarLogin($request->email, $request->password);

            return response()->success(
                data: [
                    'usuario' => new LoginResource($usuario),
                ],
                developerMessage: 'Login OK',
                userMessage: '¡Bienvenido a Meeter!',
            );
        } catch (ValidationException $exc) {
            return response()->fail(
                developerMessage: $exc->getMessage(),
                userMessage: 'Parece que los datos que ingresaste son incorrectos. Verifica tu usuario y contraseña e inténtalo de nuevo.',
                httpCode: 401
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Hubo un problema durante el inicio de sesión. Inténtalo más tarde.'
            );
        }
    }
}
