<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsVehiculos\Config\Database;
use Logitrans\MsVehiculos\Controllers\VehiculoController;
use Logitrans\MsVehiculos\Middleware\AuthMiddleware;

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
        json_encode(['mensaje' => 'Microservicio Vehiculos funcionando'])
    );
    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas — Vehículos
|--------------------------------------------------------------------------
*/

$vehiculoController = new VehiculoController();
$auth = new AuthMiddleware();

$app->get('/vehiculos', [$vehiculoController, 'listar'])->add($auth);
$app->post('/vehiculos', [$vehiculoController, 'crear'])->add($auth);
$app->get('/vehiculos/placa/{placa}', [$vehiculoController, 'buscarPorPlaca'])->add($auth);
$app->get('/vehiculos/tipo/{tipo}', [$vehiculoController, 'buscarPorTipo'])->add($auth);
$app->get('/vehiculos/estado/{estado}', [$vehiculoController, 'buscarPorEstado'])->add($auth);
$app->patch('/vehiculos/{id}/estado', [$vehiculoController, 'cambiarEstado'])->add($auth);
$app->put('/vehiculos/{id}', [$vehiculoController, 'actualizar'])->add($auth);
$app->get('/vehiculos/{id}', [$vehiculoController, 'buscarPorId'])->add($auth);

/*
|--------------------------------------------------------------------------
| Ejecutar aplicación
|--------------------------------------------------------------------------
*/

$app->run();