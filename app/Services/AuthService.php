<?php

namespace App\Services;

use App\Mail\SolicitudVerificacionRecibida;
use App\Models\User;
use App\Models\UsuarioMeeter;
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
   /*  public static function registrarse(array $datosUsuario, $imagenesGaleria = [], $imagenPerfil = null): UsuarioMeeter
    {
        try {
            $usuario = new UsuarioMeeter();

            DB::beginTransaction();

            if ( isset($datosUsuario['fecha_nacimiento']) ) {
                $datosUsuario['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $datosUsuario['fecha_nacimiento'])->format('Y-m-d');
            }

            if ( isset($datosUsuario['password']) ) {
                $datosUsuario['password'] = Hash::make($datosUsuario['password']);
            }

            if ( isset($datosUsuario['oauth_token'] ) ) {
                $datosUsuario['oauth_token'] = Crypt::encryptString($datosUsuario['oauth_token']);
            }

            $usuario->fill($datosUsuario);
            $usuario->save();

            // Guardar imágenes de la galería.
            if ( !empty($imagenesGaleria) ) {
                foreach ($imagenesGaleria as $imagen) {
                    $nombreImagen = FileService::guardarBase64EnStorage($imagen, "usuarios/{$usuario->id}/galeria");

                    $usuario->imagenes()->create([
                        'url' => $nombreImagen,
                        'tipo' => 'galeria',
                    ]);
                }
            }

            if ( $imagenPerfil ) {
                // Guardar imagen de perfil.
                $nombreImagenPerfil = FileService::guardarBase64EnStorage($imagenPerfil, "usuarios/{$usuario->id}/perfil");

                $usuario->imagenPerfil()->create([
                    'url' => $nombreImagenPerfil,
                    'tipo' => 'perfil',
                ]);
            }

            $accessToken = $usuario->createToken('access_token')->plainTextToken;
            $usuario->setAttribute('access_token', $accessToken);

            DB::commit();

            return $usuario;
        } catch (QueryException $exc) {
            DB::rollBack();

            throw $exc;
        } catch (\Exception $exc) {
            DB::rollBack();

            throw $exc;
        }
    } */

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

    /* public static function externalLogin(string $oauthToken): UsuarioMeeter
    {
        $usuarios = UsuarioMeeter::all();
        $usuarioEncontrado = null;

        foreach ($usuarios as $usuario) {
            if ( $usuario->oauth_token != null && Crypt::decryptString($usuario->oauth_token) === $oauthToken ) {
                $usuarioEncontrado = $usuario;

                break;
            }
        }

        if ( !$usuarioEncontrado ) {
            throw ValidationException::withMessages([
                'oauth_token' => ['Token no encontrado.'],
            ]);
        }

        $usuarioEncontrado->tokens()->delete();
        $accessToken = $usuarioEncontrado->createToken('access_token')->plainTextToken;
        $usuarioEncontrado->setAttribute('access_token', $accessToken);

        return $usuarioEncontrado;
    } */

    /**
     * Retorna un string que siempre tiene
     * al menos un número y una letra mayúscula.
     */
   /*  public static function generarCodigoVerificacion(int $numeroCaracteres = 4): string
    {
        if ( $numeroCaracteres == 0 ) {
            $numeroCaracteres = 4;
        }

        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigoVerificacion = '';

        $codigoVerificacion .= $caracteres[random_int(10, 35)];
        $codigoVerificacion .= $caracteres[random_int(0, 9)];

        for ($i = 0; $i < $numeroCaracteres - 2; $i++) {
            $codigoVerificacion .= $caracteres[random_int(0, 35)];
        }

        return $codigoVerificacion;
    } */

    /* public static function actualizarPerfil(array $datosUsuario, $imagenesRemovidas = [], $imagenesNuevas = [], $imagenPerfil = null)
    {
        try {
            $usuario = Auth::user();

            DB::beginTransaction();

            $datosUsuario['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $datosUsuario['fecha_nacimiento'])->format('Y-m-d');

            $usuario->fill($datosUsuario);
            $usuario->save();

            // Eliminar imágenes que el usuario quitó.
            if ( !empty($imagenesRemovidas) ) {
                $imagenes = $usuario->imagenes()->whereIn('id', $imagenesRemovidas);

                $imagenes->each(function ($imagen) {
                    $imagen->delete();
                });
            }

            // Agregar imágenes de la galería.
            if ( !empty($imagenesNuevas) ) {
                foreach ($imagenesNuevas as $imagenNueva) {
                    $nombreImagen = FileService::guardarBase64EnStorage($imagenNueva, "usuarios/{$usuario->id}/galeria");

                    $usuario->imagenes()->create([
                        'url' => $nombreImagen,
                        'tipo' => 'galeria',
                    ]);
                }
            }

            // Actualizar imagen de perfil.
            if ( $imagenPerfil ) {
                $usuario->imagenPerfil()?->first()?->delete();
                $nombreImagen = FileService::guardarBase64EnStorage($imagenPerfil, "usuarios/{$usuario->id}/perfil");

                $usuario->imagenPerfil()->create([
                    'url' => $nombreImagen,
                    'tipo' => 'perfil',
                ]);
            }

            DB::commit();
        } catch (QueryException $exc) {
            DB::rollBack();

            throw $exc;
        } catch (\Exception $exc) {
            DB::rollBack();

            throw $exc;
        }
    } */

    /* public static function actualizarPassword(string $email, string $password): void
    {
        $usuario = UsuarioMeeter::where('email', $email)->firstOrFail();

        $usuario->password = Hash::make($password);
        $usuario->save();
    } */

    /**
     * Se eliminan las imágenes de tipo cedula
     * y se guardan otras nuevas.
     */
    /* public static function actualizarCedula(string $cedulaFrontal, string $cedulaPosterior): void
    {
        $usuario = Auth::user();

        try {
            DB::beginTransaction();

            $usuario->imagenesCedula()->each(function ($imagen) {
                $imagen->delete();
            });

            // Cédula frontal.
            $nombreImagenFrontal = FileService::guardarBase64EnStorage($cedulaFrontal, "usuarios/{$usuario->id}/cedula");

            $usuario->imagenesCedula()->create([
                'url' => $nombreImagenFrontal,
                'tipo' => 'cedula',
            ]);

            // Cédula posterior.
            $nombreImagenPosterior = FileService::guardarBase64EnStorage($cedulaPosterior, "usuarios/{$usuario->id}/cedula");

            $usuario->imagenesCedula()->create([
                'url' => $nombreImagenPosterior,
                'tipo' => 'cedula',
            ]);

            DB::commit();
        } catch (QueryException $exc) {
            DB::rollBack();

            throw $exc;
        } catch (FileException $exc) {
            DB::rollBack();

            throw $exc;
        }
    } */

    /* public static function solicitarVerificacion(): void
    {
        $usuario = Auth::user();
        $usuario->verificacion = 'Pendiente';
        $usuario->save();

        $administradores = User::where('estado', true)->pluck('email');

        Mail::to($administradores)->send(new SolicitudVerificacionRecibida($usuario));
    } */

    /* public static function ocultarCuenta(string $motivo): void
    {
        try {
            $usuario = Auth::user();

            DB::beginTransaction();

            $usuario->visibilidad = false;
            $usuario->save();

            $usuario->motivoAcciones()->create([
                'motivo' => $motivo,
                'accion' => 2,
            ]);

            $usuario->solicitudesEspera()->delete();

            DB::commit();
        } catch (QueryException $exc) {
            DB::rollBack();

            throw $exc;
        }
    } */

    /* public static function eliminarCuenta(string $motivo)
    {
        try {
            $usuario = Auth::user();

            DB::beginTransaction();

            $usuario->motivoAcciones()->create([
                'motivo' => $motivo,
                'accion' => 3,
            ]);

            $usuario->tokens()->delete();
            $usuario->delete();

            DB::commit();
        } catch (QueryException $exc) {
            DB::rollback();

            throw $exc;
        } catch (\Exception $exc) {
            DB::rollback();

            throw $exc;
        }
    } */
}
