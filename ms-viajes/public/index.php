<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsViajes\Config\Database;
use Logitrans\MsViajes\Controllers\SeguimientoViajeController;

$dotenv = Dotenv::createImmutable(
    __DIR__ . '/../'
);

$dotenv->load();

Database::connect();

$app = AppFactory::create();

$seguimientoController =
    new SeguimientoViajeController();

$app->get('/', function ($request, $response) {

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Microservicio Viajes funcionando'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get(
    '/seguimientos',
    [$seguimientoController, 'listar']
);

$app->get(
    '/seguimientos/{id}',
    [$seguimientoController, 'buscarPorId']
);

$app->get(
    '/seguimientos/programacion/{programacion_id}',
    [$seguimientoController, 'buscarPorProgramacion']
);

$app->post(
    '/seguimientos/iniciar',
    [$seguimientoController, 'iniciarViaje']
);

$app->post(
    '/seguimientos/novedad',
    [$seguimientoController, 'registrarNovedad']
);

$app->patch(
    '/seguimientos/finalizar',
    [$seguimientoController, 'finalizarViaje']
);

$app->run();