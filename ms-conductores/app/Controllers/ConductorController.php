<?php

namespace Logitrans\MsConductores\Controllers;

use Logitrans\MsConductores\Models\Conductor;

class ConductorController
{
    public function listar($request, $response)
    {
        $conductores = Conductor::all();

        $response->getBody()->write(
            $conductores->toJson()
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

    if (
    strtotime($data['fecha_vencimiento_licencia'])
    <= strtotime(date('Y-m-d'))
) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'La licencia se encuentra vencida'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

    $documentoExiste = Conductor::where(
        'documento',
        $data['documento']
    )->exists();

    if ($documentoExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Documento ya registrado'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $licenciaExiste = Conductor::where(
        'numero_licencia',
        $data['numero_licencia']
    )->exists();

    if ($licenciaExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Licencia ya registrada'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $conductor = Conductor::create([
        'nombres' => $data['nombres'],
        'apellidos' => $data['apellidos'],
        'documento' => $data['documento'],
        'telefono' => $data['telefono'],
        'correo' => $data['correo'],
        'numero_licencia' => $data['numero_licencia'],
        'categoria_licencia' => $data['categoria_licencia'],
        'fecha_vencimiento_licencia' => $data['fecha_vencimiento_licencia'],
        'estado' => 'disponible'
    ]);

    $response->getBody()->write(
        $conductor->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorDocumento($request, $response, $args)
{
    $documento = $args['documento'];

    $conductor = Conductor::where(
        'documento',
        $documento
    )->first();

    if (!$conductor) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Conductor no encontrado'
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
        $conductor->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorLicencia($request, $response, $args)
{
    $licencia = $args['licencia'];

    $conductor = Conductor::where(
        'numero_licencia',
        $licencia
    )->first();

    if (!$conductor) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Conductor no encontrado'
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
        $conductor->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

public function buscarPorEstado($request, $response, $args)
{
    $estado = $args['estado'];

    $conductores = Conductor::where(
        'estado',
        $estado
    )->get();

    $response->getBody()->write(
        $conductores->toJson()
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

    $conductor = Conductor::find($id);

    if (!$conductor) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Conductor no encontrado'
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

    $conductor->estado = $data['estado'];

    $conductor->save();

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $conductor->estado
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

    $conductor = Conductor::find($id);

    if (!$conductor) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Conductor no encontrado'
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

    if (
    strtotime($data['fecha_vencimiento_licencia'])
    <= strtotime(date('Y-m-d'))
) {

    $response->getBody()->write(
        json_encode([
            'success' => false,
            'mensaje' => 'La licencia se encuentra vencida'
        ])
    );

    return $response
        ->withHeader(
            'Content-Type',
            'application/json'
        )
        ->withStatus(400);
}

    $correoExiste = Conductor::where(
        'correo',
        $data['correo']
    )
    ->where('id', '!=', $id)
    ->exists();

    if ($correoExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Correo ya registrado'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $licenciaExiste = Conductor::where(
        'numero_licencia',
        $data['numero_licencia']
    )
    ->where('id', '!=', $id)
    ->exists();

    if ($licenciaExiste) {

        $response->getBody()->write(
            json_encode([
                'success' => false,
                'mensaje' => 'Licencia ya registrada'
            ])
        );

        return $response
            ->withHeader(
                'Content-Type',
                'application/json'
            )
            ->withStatus(400);
    }

    $conductor->update([
        'nombres' => $data['nombres'],
        'apellidos' => $data['apellidos'],
        'telefono' => $data['telefono'],
        'correo' => $data['correo'],
        'numero_licencia' => $data['numero_licencia'],
        'categoria_licencia' => $data['categoria_licencia'],
        'fecha_vencimiento_licencia' => $data['fecha_vencimiento_licencia'],
        'estado' => $data['estado']
    ]);

    $response->getBody()->write(
        json_encode([
            'success' => true,
            'mensaje' => 'Conductor actualizado correctamente'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
}

}