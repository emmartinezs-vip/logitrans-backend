<?php

namespace Logitrans\MsViajes\Controllers;

use Logitrans\MsViajes\Models\SeguimientoViaje;

class SeguimientoViajeController
{
    public function listar($request, $response)
    {
        $seguimientos = SeguimientoViaje::all();

        $response->getBody()->write(
            $seguimientos->toJson()
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function buscarPorId($request, $response, $args)
    {
        $seguimiento = SeguimientoViaje::find($args['id']);

        if (!$seguimiento) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Seguimiento no encontrado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $response->getBody()->write(
            $seguimiento->toJson()
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function buscarPorProgramacion($request, $response, $args)
    {
        $seguimientos = SeguimientoViaje::where(
            'programacion_viaje_id',
            $args['programacion_id']
        )->get();

        $response->getBody()->write(
            $seguimientos->toJson()
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function iniciarViaje($request, $response)
    {
        $data = json_decode(
            $request->getBody()->getContents(),
            true
        );

        $ultimoSeguimiento = SeguimientoViaje::where(
            'programacion_viaje_id',
            $data['programacion_viaje_id']
        )
        ->orderBy('id', 'desc')
        ->first();

        // Validar viaje cancelado
        if (
            $ultimoSeguimiento &&
            $ultimoSeguimiento->estado === 'cancelado'
        ) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'No se puede iniciar un viaje cancelado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Validar viaje ya iniciado
        if (
            $ultimoSeguimiento &&
            $ultimoSeguimiento->estado === 'en_transito'
        ) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'El viaje ya fue iniciado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Validar viaje ya finalizado
        if (
            $ultimoSeguimiento &&
            $ultimoSeguimiento->estado === 'finalizado'
        ) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'El viaje ya fue finalizado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $seguimiento = SeguimientoViaje::create([
            'programacion_viaje_id' => $data['programacion_viaje_id'],
            'fecha'                 => date('Y-m-d'),
            'hora'                  => date('H:i:s'),
            'estado'                => 'en_transito',
            'novedad'               => 'Viaje iniciado'
        ]);

        $response->getBody()->write(
            json_encode([
                'success' => true,
                'mensaje' => 'Viaje iniciado correctamente',
                'data'    => $seguimiento
            ])
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function registrarNovedad($request, $response)
    {
        $data = json_decode(
            $request->getBody()->getContents(),
            true
        );

        $estadosPermitidos = [
            'en_transito',
            'retrasado',
            'cancelado'
        ];

        if (!in_array($data['estado'], $estadosPermitidos)) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'Estado no válido'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        SeguimientoViaje::create([
            'programacion_viaje_id' => $data['programacion_viaje_id'],
            'fecha'                 => date('Y-m-d'),
            'hora'                  => date('H:i:s'),
            'estado'                => $data['estado'],
            'novedad'               => $data['novedad']
        ]);

        $response->getBody()->write(
            json_encode([
                'success' => true,
                'mensaje' => 'Novedad registrada correctamente'
            ])
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    public function finalizarViaje($request, $response)
    {
        $data = json_decode(
            $request->getBody()->getContents(),
            true
        );

        $ultimoSeguimiento = SeguimientoViaje::where(
            'programacion_viaje_id',
            $data['programacion_viaje_id']
        )
        ->orderBy('id', 'desc')
        ->first();

        if (!$ultimoSeguimiento) {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'El viaje no ha sido iniciado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        if ($ultimoSeguimiento->estado === 'cancelado') {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'No se puede finalizar un viaje cancelado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        if ($ultimoSeguimiento->estado === 'finalizado') {
            $response->getBody()->write(
                json_encode([
                    'success' => false,
                    'mensaje' => 'El viaje ya fue finalizado'
                ])
            );
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        SeguimientoViaje::create([
            'programacion_viaje_id' => $data['programacion_viaje_id'],
            'fecha'                 => date('Y-m-d'),
            'hora'                  => date('H:i:s'),
            'estado'                => 'finalizado',
            'novedad'               => 'Viaje finalizado correctamente'
        ]);

        $response->getBody()->write(
            json_encode([
                'success' => true,
                'mensaje' => 'Viaje finalizado correctamente'
            ])
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }
}