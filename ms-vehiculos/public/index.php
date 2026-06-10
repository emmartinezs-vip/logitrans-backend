<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Logitrans\MsVehiculos\Config\Database;
use Logitrans\MsVehiculos\Models\Vehiculo;
use Logitrans\MsVehiculos\Controllers\VehiculoController;

$dotenv = Dotenv::createImmutable(
    __DIR__ . '/../'
);

$dotenv->load();

Database::connect();

$app = AppFactory::create();

$vehiculoController = new VehiculoController();

$app->get('/', function ($request, $response) {

    $response->getBody()->write(
        json_encode([
            "mensaje" => "Microservicio Vehiculos funcionando"
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get(
    '/vehiculos',
    [$vehiculoController, 'listar']
);

$app->post(
    '/vehiculos',
    [$vehiculoController, 'crear']
);

$app->get(
    '/vehiculos/placa/{placa}',
    [$vehiculoController, 'buscarPorPlaca']
);

$app->get(
    '/vehiculos/tipo/{tipo}',
    [$vehiculoController, 'buscarPorTipo']
);

$app->get(
    '/vehiculos/estado/{estado}',
    [$vehiculoController, 'buscarPorEstado']
);

$app->patch(
    '/vehiculos/{id}/estado',
    [$vehiculoController, 'cambiarEstado']
);

$app->put(
    '/vehiculos/{id}',
    [$vehiculoController, 'actualizar']
);

$app->run();