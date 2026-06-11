<?php

namespace Logitrans\MsRutas\Controllers;

use Logitrans\MsRutas\Models\ProgramacionViaje;

class ProgramacionViajeController
{

private function consumirApi($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $respuesta = curl_exec($curl);

    curl_close($curl);

    return json_decode(
        $respuesta,
        true
    );
}
    public function listar($request, $response)
    {
        $programaciones = ProgramacionViaje::all();

        $response->getBody()->write(
            $programaciones->toJson()
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

$conductor = $this->consumirApi(
    'http://localhost:8001/conductores/' .
    $data['conductor_id']
);

$vehiculo = $this->consumirApi(
    'http://localhost:8002/vehiculos/' .
    $data['vehiculo_id']
);

if (
    isset($conductor['estado']) &&
    $conductor['estado'] === 'inactivo'
) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'El conductor está inactivo'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

if (
    isset($vehiculo['estado']) &&
    $vehiculo['estado'] === 'mantenimiento'
) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'El vehículo está en mantenimiento'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

$programacionConductor = ProgramacionViaje::where(
    'conductor_id',
    $data['conductor_id']
)
->whereIn(
    'estado',
    [
        'programado',
        'en_transito',
        'retrasado'
    ]
)
->exists();

if ($programacionConductor) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'El conductor ya tiene un viaje asignado'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

$programacionVehiculo = ProgramacionViaje::where(
    'vehiculo_id',
    $data['vehiculo_id']
)
->whereIn(
    'estado',
    [
        'programado',
        'en_transito',
        'retrasado'
    ]
)
->exists();

if ($programacionVehiculo) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'El vehículo ya tiene un viaje asignado'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

    $programacion = ProgramacionViaje::create([
        'conductor_id' => $data['conductor_id'],
        'vehiculo_id' => $data['vehiculo_id'],
        'ruta_id' => $data['ruta_id'],
        'fecha_salida' => $data['fecha_salida'],
        'hora_salida' => $data['hora_salida'],
        'fecha_estimada_llegada' => $data['fecha_estimada_llegada'],
        'observaciones' => $data['observaciones'],
        'estado' => 'programado'
    ]);

    $response->getBody()->write(
        $programacion->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorId($request, $response, $args)
{
    $programacion = ProgramacionViaje::find(
        $args['id']
    );

    if (!$programacion) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Programación no encontrada'
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
        $programacion->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorConductor($request, $response, $args)
{
    $programaciones = ProgramacionViaje::where(
        'conductor_id',
        $args['id']
    )->get();

    $response->getBody()->write(
        $programaciones->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorVehiculo($request, $response, $args)
{
    $programaciones = ProgramacionViaje::where(
        'vehiculo_id',
        $args['id']
    )->get();

    $response->getBody()->write(
        $programaciones->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorEstado($request, $response, $args)
{
    $programaciones = ProgramacionViaje::where(
        'estado',
        $args['estado']
    )->get();

    $response->getBody()->write(
        $programaciones->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorFecha($request, $response, $args)
{
    $programaciones = ProgramacionViaje::where(
        'fecha_salida',
        $args['fecha']
    )->get();

    $response->getBody()->write(
        $programaciones->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function actualizar($request, $response, $args)
{
    $programacion = ProgramacionViaje::find(
        $args['id']
    );

    if (!$programacion) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Programación no encontrada'
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

    $estadosPermitidos = [
        'programado',
        'en_transito',
        'retrasado',
        'finalizado',
        'cancelado'
    ];

    if (
        !in_array(
            $data['estado'],
            $estadosPermitidos
        )
    ) {

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

    $programacion->update([
        'fecha_salida' => $data['fecha_salida'],
        'hora_salida' => $data['hora_salida'],
        'fecha_estimada_llegada' => $data['fecha_estimada_llegada'],
        'observaciones' => $data['observaciones'],
        'estado' => $data['estado']
    ]);

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Programación actualizada correctamente'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

}