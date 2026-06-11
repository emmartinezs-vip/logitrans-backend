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

public function buscarPorTipo($request, $response, $args)
{
    $tipo = $args['tipo'];

    $vehiculos = Vehiculo::where(
        'tipo_vehiculo',
        $tipo
    )->get();

    $response->getBody()->write(
        $vehiculos->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorEstado($request, $response, $args)
{
    $estado = $args['estado'];

    $vehiculos = Vehiculo::where(
        'estado',
        $estado
    )->get();

    $response->getBody()->write(
        $vehiculos->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function cambiarEstado($request, $response, $args)
{
    $id = $args['id'];

    $data = json_decode(
        $request->getBody()->getContents(),
        true
    );

    $vehiculo = Vehiculo::find($id);

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

    $estadosPermitidos = [
        'disponible',
        'en_ruta',
        'mantenimiento',
        'inactivo'
    ];

    if (!in_array(
        $data['estado'],
        $estadosPermitidos
    )) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Estado no válido'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $vehiculo->estado = $data['estado'];

    $vehiculo->save();

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $vehiculo->estado
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function actualizar($request, $response, $args)
{
    $id = $args['id'];

    $vehiculo = Vehiculo::find($id);

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

    $data = json_decode(
        $request->getBody()->getContents(),
        true
    );

    $placaExiste = Vehiculo::where(
        'placa',
        $data['placa']
    )
    ->where('id', '!=', $id)
    ->exists();

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

    $vehiculo->update([
        'placa' => $data['placa'],
        'tipo_vehiculo' => $data['tipo_vehiculo'],
        'capacidad_carga' => $data['capacidad_carga'],
        'modelo' => $data['modelo'],
        'marca' => $data['marca'],
        'estado' => $data['estado']
    ]);

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Vehículo actualizado correctamente'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorId($request, $response, $args)
{
    $vehiculo = Vehiculo::find(
        $args['id']
    );

    if (!$vehiculo) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Vehiculo no encontrado'
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