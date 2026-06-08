<?php

namespace Logitrans\MsAuth\Middleware;

use Logitrans\MsAuth\Models\Usuario;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization)) {

            $response = new \Slim\Psr7\Response();

            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Token requerido'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $token = str_replace(
            'Bearer ',
            '',
            $authorization
        );

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {

            $response = new \Slim\Psr7\Response();

            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Token inválido'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        return $handler->handle($request);
    }
}