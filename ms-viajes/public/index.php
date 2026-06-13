<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsViajes\Config\Database;
use Logitrans\MsViajes\Controllers\SeguimientoViajeController;
use Logitrans\MsViajes\Middleware\AuthMiddleware;

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
        json_encode(['mensaje' => 'Microservicio Viajes funcionando'])
    );
    return $response->withHeader('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas — Seguimientos
| IMPORTANTE: rutas específicas ANTES que las paramétricas /{id}
|--------------------------------------------------------------------------
*/

$seguimientoController = new SeguimientoViajeController();
$auth = new AuthMiddleware();

$app->get('/seguimientos', [$seguimientoController, 'listar'])->add($auth);
$app->get('/seguimientos/programacion/{programacion_id}', [$seguimientoController, 'buscarPorProgramacion'])->add($auth);
$app->post('/seguimientos/iniciar', [$seguimientoController, 'iniciarViaje'])->add($auth);
$app->post('/seguimientos/novedad', [$seguimientoController, 'registrarNovedad'])->add($auth);
$app->patch('/seguimientos/finalizar', [$seguimientoController, 'finalizarViaje'])->add($auth);
$app->get('/seguimientos/{id}', [$seguimientoController, 'buscarPorId'])->add($auth);

/*
|--------------------------------------------------------------------------
| Ejecutar aplicación
|--------------------------------------------------------------------------
*/

$app->run();