<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsAuth\Config\Database;
use Logitrans\MsAuth\Models\Usuario;
use Logitrans\MsAuth\Controllers\AuthController;
use Logitrans\MsAuth\Middleware\AuthMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Database::connect();

$app = AppFactory::create();

/*
|--------------------------------------------------------------------------
| Manejo de CORS
|--------------------------------------------------------------------------
*/

$app->add(function ($request, $handler) {
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withStatus(200);
    }

    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

/*
|--------------------------------------------------------------------------
| Ruta de prueba
|--------------------------------------------------------------------------
*/

$app->get('/', function ($request, $response) {
    $response->getBody()->write(
        json_encode(['mensaje' => 'Microservicio Auth funcionando'])
    );
    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Ruta protegida — Usuarios
|--------------------------------------------------------------------------
*/

$app->get('/usuarios', function ($request, $response) {
    $usuarios = Usuario::all();
    $response->getBody()->write($usuarios->toJson());
    return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());

/*
|--------------------------------------------------------------------------
| Auth — Login y Logout
|--------------------------------------------------------------------------
*/

$authController = new AuthController();

$app->post('/login', [$authController, 'login']);
$app->post('/logout', [$authController, 'logout']);

/*
|--------------------------------------------------------------------------
| Validación de token para otros microservicios
|--------------------------------------------------------------------------
*/

$app->get('/validar', function ($request, $response) {

    $authorization = $request->getHeaderLine('Authorization');

    if (empty($authorization)) {
        $response->getBody()->write(
            json_encode(['valid' => false, 'mensaje' => 'Token requerido'])
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

    $token = str_replace('Bearer ', '', $authorization);

    $usuario = Usuario::where('token', $token)
        ->where('sesion_activa', true)
        ->first();

    if (!$usuario) {
        $response->getBody()->write(
            json_encode(['valid' => false, 'mensaje' => 'Token inválido'])
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

    $response->getBody()->write(
        json_encode([
            'valid'   => true,
            'usuario' => [
                'id'     => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol'    => $usuario->rol
            ]
        ])
    );

    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Ejecutar aplicación
|--------------------------------------------------------------------------
*/

$app->run();