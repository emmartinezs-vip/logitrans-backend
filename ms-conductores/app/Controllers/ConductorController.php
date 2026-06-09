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
}