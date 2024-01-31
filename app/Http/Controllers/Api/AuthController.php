<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
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
                userMessage: '¡Bienvenido!',
            );
        } catch (ValidationException $exc) {
            return response()->fail(
                developerMessage: $exc->getMessage(),
                userMessage: 'Verify you email and password, and try again.',
                httpCode: 401
            );
        } catch (\Exception $exc) {
            return response()->error(
                developerMessage: $exc->getMessage(),
                userMessage: 'Verify you email and password, and try again.'
            );
        }
    }
}
