<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HerramientaType extends Model
{
    use HasFactory;

    protected $table = 'herramientas_entrenamiento_type';
    protected $fillable = ['name', 'icon'];

    public function herramientas()
    {
        return $this->hasMany(Herramienta::class, 'type_id');
    }
}
