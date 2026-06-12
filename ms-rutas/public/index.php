<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsRutas\Config\Database;
use Logitrans\MsRutas\Controllers\RutaController;
use Logitrans\MsRutas\Controllers\ProgramacionViajeController;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Conectar base de datos
Database::connect();

// Crear aplicación
$app = AppFactory::create();

$app->options('/{routes:.+}', function (
    $request,
    $response
) {
    return $response;
});

$app->add(function ($request, $handler) {

    $response = $handler->handle($request);

    return $response
        ->withHeader(
            'Access-Control-Allow-Origin',
            '*'
        )
        ->withHeader(
            'Access-Control-Allow-Headers',
            'Content-Type, Authorization'
        )
        ->withHeader(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, PATCH, DELETE, OPTIONS'
        );
});

$rutaController = new RutaController();

$programacionController = new ProgramacionViajeController();

// Ruta de prueba
$app->get('/', function ($request, $response) {

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Microservicio Rutas funcionando'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get(
    '/rutas',
    [$rutaController, 'listar']
);

$app->post(
    '/rutas',
    [$rutaController, 'crear']
);

$app->get(
    '/rutas/{id}',
    [$rutaController, 'buscarPorId']
);

$app->put(
    '/rutas/{id}',
    [$rutaController, 'actualizar']
);

$app->get(
    '/rutas/ciudad/{ciudad}',
    [$rutaController, 'buscarPorCiudad']
);

$app->get(
    '/rutas/tiempo/{id}',
    [$rutaController, 'tiempoEstimado']
);

$app->get(
    '/programaciones',
    [$programacionController, 'listar']
);

$app->post(
    '/programaciones',
    [$programacionController, 'crear']
);

$app->get(
    '/programaciones/{id}',
    [$programacionController, 'buscarPorId']
);

$app->get(
    '/programaciones/conductor/{id}',
    [$programacionController, 'buscarPorConductor']
);

$app->get(
    '/programaciones/vehiculo/{id}',
    [$programacionController, 'buscarPorVehiculo']
);

$app->get(
    '/programaciones/estado/{estado}',
    [$programacionController, 'buscarPorEstado']
);

$app->get(
    '/programaciones/fecha/{fecha}',
    [$programacionController, 'buscarPorFecha']
);

$app->put(
    '/programaciones/{id}',
    [$programacionController, 'actualizar']
);

$app->run();