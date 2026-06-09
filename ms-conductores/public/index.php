<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsConductores\Config\Database;
use Logitrans\MsConductores\Controllers\ConductorController;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Conectar a la base de datos
Database::connect();

// Crear aplicación Slim
$app = AppFactory::create();

// Ruta de prueba
$app->get('/', function ($request, $response) {

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Microservicio Conductores funcionando'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$conductorController = new ConductorController();

$app->get(
    '/conductores',
    [$conductorController, 'listar']
);

$app->post(
    '/conductores',
    [$conductorController, 'crear']
);

$app->get(
    '/conductores/documento/{documento}',
    [$conductorController, 'buscarPorDocumento']
);

$app->get(
    '/conductores/licencia/{licencia}',
    [$conductorController, 'buscarPorLicencia']
);

$app->get(
    '/conductores/estado/{estado}',
    [$conductorController, 'buscarPorEstado']
);

$app->patch(
    '/conductores/{id}/estado',
    [$conductorController, 'cambiarEstado']
);

$app->put(
    '/conductores/{id}',
    [$conductorController, 'actualizar']
);

$app->run();