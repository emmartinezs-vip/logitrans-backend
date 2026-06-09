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

$app->run();