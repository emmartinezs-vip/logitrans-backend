<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsAuth\Config\Database;
use Logitrans\MsAuth\Models\Usuario;
use Logitrans\MsAuth\Controllers\AuthController;

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
            "mensaje" => "Microservicio Auth funcionando"
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

// Ruta para listar usuarios
$app->get('/usuarios', function ($request, $response) {

    $usuarios = Usuario::all();

    $response->getBody()->write(
        $usuarios->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$authController = new AuthController();

$app->post('/login', [$authController, 'login']);

$app->run();