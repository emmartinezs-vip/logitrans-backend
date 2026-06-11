<?php

namespace Logitrans\MsRutas\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';

    protected $fillable = [
        'ciudad_origen',
        'ciudad_destino',
        'distancia',
        'tiempo_estimado',
        'observaciones'
    ];
}
