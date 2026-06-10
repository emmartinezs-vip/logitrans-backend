<?php

namespace Logitrans\MsVehiculos\Controllers;

use Logitrans\MsVehiculos\Models\Vehiculo;

class VehiculoController
{
    public function listar($request, $response)
    {
        $vehiculos = Vehiculo::all();

        $response->getBody()->write(
            $vehiculos->toJson()
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function crear($request, $response)
{
    $data = json_decode(
        $request->getBody()->getContents(),
        true
    );

    if ($data['capacidad_carga'] <= 0) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La capacidad debe ser mayor a cero'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $placaExiste = Vehiculo::where(
        'placa',
        $data['placa']
    )->exists();

    if ($placaExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La placa ya existe'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $vehiculo = Vehiculo::create([
        'placa' => $data['placa'],
        'tipo_vehiculo' => $data['tipo_vehiculo'],
        'capacidad_carga' => $data['capacidad_carga'],
        'modelo' => $data['modelo'],
        'marca' => $data['marca'],
        'estado' => 'disponible'
    ]);

    $response->getBody()->write(
        $vehiculo->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorPlaca($request, $response, $args)
{
    $placa = $args['placa'];

    $vehiculo = Vehiculo::where(
        'placa',
        $placa
    )->first();

    if (!$vehiculo) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Vehículo no encontrado'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(404);
    }

    $response->getBody()->write(
        $vehiculo->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

}