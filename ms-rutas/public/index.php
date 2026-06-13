<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsRutas\Config\Database;
use Logitrans\MsRutas\Controllers\RutaController;
use Logitrans\MsRutas\Controllers\ProgramacionViajeController;
use Logitrans\MsRutas\Middleware\AuthMiddleware;

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
        json_encode(['mensaje' => 'Microservicio Rutas funcionando'])
    );
    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas — Rutas
| IMPORTANTE: rutas específicas ANTES que las paramétricas /{id}
|--------------------------------------------------------------------------
*/

$rutaController = new RutaController();
$programacionController = new ProgramacionViajeController();
$auth = new AuthMiddleware();

$app->get('/rutas', [$rutaController, 'listar'])->add($auth);
$app->post('/rutas', [$rutaController, 'crear'])->add($auth);
$app->get('/rutas/ciudad/{ciudad}', [$rutaController, 'buscarPorCiudad'])->add($auth);
$app->get('/rutas/tiempo/{id}', [$rutaController, 'tiempoEstimado'])->add($auth);
$app->put('/rutas/{id}', [$rutaController, 'actualizar'])->add($auth);
$app->get('/rutas/{id}', [$rutaController, 'buscarPorId'])->add($auth);

/*
|--------------------------------------------------------------------------
| Rutas protegidas — Programaciones
| IMPORTANTE: rutas específicas ANTES que las paramétricas /{id}
|--------------------------------------------------------------------------
*/

$app->get('/programaciones', [$programacionController, 'listar'])->add($auth);
$app->post('/programaciones', [$programacionController, 'crear'])->add($auth);
$app->get('/programaciones/conductor/{id}', [$programacionController, 'buscarPorConductor'])->add($auth);
$app->get('/programaciones/vehiculo/{id}', [$programacionController, 'buscarPorVehiculo'])->add($auth);
$app->get('/programaciones/estado/{estado}', [$programacionController, 'buscarPorEstado'])->add($auth);
$app->get('/programaciones/fecha/{fecha}', [$programacionController, 'buscarPorFecha'])->add($auth);
$app->put('/programaciones/{id}', [$programacionController, 'actualizar'])->add($auth);
$app->get('/programaciones/{id}', [$programacionController, 'buscarPorId'])->add($auth);

/*
|--------------------------------------------------------------------------
| Ejecutar aplicación
|--------------------------------------------------------------------------
*/

$app->run();