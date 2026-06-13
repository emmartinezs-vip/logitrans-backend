<?php

namespace Logitrans\MsViajes\Middleware;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(
                json_encode(['success' => false, 'mensaje' => 'Token requerido'])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'http://localhost:8000/validar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: ' . $authorization]
        ]);
        $respuesta = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($respuesta, true);

        if (!$data || !isset($data['valid']) || !$data['valid']) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(
                json_encode(['success' => false, 'mensaje' => 'Token inválido o sesión expirada'])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        return $handler->handle($request);
    }
}