<?php

namespace Logitrans\MsRutas\Controllers;

use Logitrans\MsRutas\Models\Ruta;

class RutaController
{
    public function listar($request, $response)
    {
        $rutas = Ruta::all();

        $response->getBody()->write(
            $rutas->toJson()
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

    $rutaExiste = Ruta::where(
        'ciudad_origen',
        $data['ciudad_origen']
    )
    ->where(
        'ciudad_destino',
        $data['ciudad_destino']
    )
    ->exists();

    if ($rutaExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La ruta ya existe'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    if ($data['distancia'] <= 0) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La distancia debe ser mayor a cero'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $ruta = Ruta::create([
        'ciudad_origen' => $data['ciudad_origen'],
        'ciudad_destino' => $data['ciudad_destino'],
        'distancia' => $data['distancia'],
        'tiempo_estimado' => $data['tiempo_estimado'],
        'observaciones' => $data['observaciones']
    ]);

    $response->getBody()->write(
        $ruta->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorId($request, $response, $args)
{
    $ruta = Ruta::find($args['id']);

    if (!$ruta) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Ruta no encontrada'
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
        $ruta->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function actualizar($request, $response, $args)
{
    $id = $args['id'];

    $ruta = Ruta::find($id);

    if (!$ruta) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Ruta no encontrada'
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

    if ($data['distancia'] <= 0) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La distancia debe ser mayor a cero'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $rutaDuplicada = Ruta::where(
        'ciudad_origen',
        $data['ciudad_origen']
    )
    ->where(
        'ciudad_destino',
        $data['ciudad_destino']
    )
    ->where(
        'id',
        '!=',
        $id
    )
    ->exists();

    if ($rutaDuplicada) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'La ruta ya existe'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $ruta->update([
        'ciudad_origen' => $data['ciudad_origen'],
        'ciudad_destino' => $data['ciudad_destino'],
        'distancia' => $data['distancia'],
        'tiempo_estimado' => $data['tiempo_estimado'],
        'observaciones' => $data['observaciones']
    ]);

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Ruta actualizada correctamente'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorCiudad($request, $response, $args)
{
    $ciudad = $args['ciudad'];

    $rutas = Ruta::where(
        'ciudad_origen',
        'LIKE',
        "%{$ciudad}%"
    )
    ->orWhere(
        'ciudad_destino',
        'LIKE',
        "%{$ciudad}%"
    )
    ->get();

    $response->getBody()->write(
        $rutas->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function tiempoEstimado($request, $response, $args)
{
    $ruta = Ruta::find($args['id']);

    if (!$ruta) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Ruta no encontrada'
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
        json_encode([
            'ruta_id' => $ruta->id,
            'origen' => $ruta->ciudad_origen,
            'destino' => $ruta->ciudad_destino,
            'tiempo_estimado' => $ruta->tiempo_estimado
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

}