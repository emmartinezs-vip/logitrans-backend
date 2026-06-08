<?php

namespace Logitrans\MsAuth\Controllers;

use Logitrans\MsAuth\Models\Usuario;

class AuthController
{
    public function login($request, $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $usuarioInput = $data['usuario'] ?? '';
        $contrasena = $data['contrasena'] ?? '';

        $usuario = Usuario::where('usuario', $usuarioInput)
            ->orWhere('correo', $usuarioInput)
            ->first();

        if (!$usuario) {

            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Usuario no encontrado'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        if ($usuario->contrasena !== $contrasena) {

            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Contraseña incorrecta'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $token = bin2hex(random_bytes(32));

        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        $response->getBody()->write(
            json_encode([
                'success' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'token' => $token,
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol
                ]
            ])
        );

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}