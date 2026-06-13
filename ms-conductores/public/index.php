<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsConductores\Config\Database;
use Logitrans\MsConductores\Controllers\ConductorController;
use Logitrans\MsConductores\Middleware\AuthMiddleware;

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
        json_encode(['mensaje' => 'Microservicio Conductores funcionando'])
    );
    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas — Conductores
|--------------------------------------------------------------------------
*/

$conductorController = new ConductorController();
$auth = new AuthMiddleware();

$app->get('/conductores', [$conductorController, 'listar'])->add($auth);
$app->post('/conductores', [$conductorController, 'crear'])->add($auth);
$app->get('/conductores/documento/{documento}', [$conductorController, 'buscarPorDocumento'])->add($auth);
$app->get('/conductores/licencia/{licencia}', [$conductorController, 'buscarPorLicencia'])->add($auth);
$app->get('/conductores/estado/{estado}', [$conductorController, 'buscarPorEstado'])->add($auth);
$app->patch('/conductores/{id}/estado', [$conductorController, 'cambiarEstado'])->add($auth);
$app->put('/conductores/{id}', [$conductorController, 'actualizar'])->add($auth);
$app->get('/conductores/{id}', [$conductorController, 'buscarPorId'])->add($auth);

/*
|--------------------------------------------------------------------------
| Ejecutar aplicación
|--------------------------------------------------------------------------
*/

$app->run();