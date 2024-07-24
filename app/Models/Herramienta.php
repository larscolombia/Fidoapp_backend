<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Herramienta extends Model
{
    use HasFactory;

    protected $table = 'herramientas_entrenamiento';
    protected $guarded = [];

    public function type()
    {
        return $this->belongsTo(HerramientaType::class, 'type_id');
    }
}
