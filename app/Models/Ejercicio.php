<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = 'ejercicios';
    protected $guarded = [];

    public function clase()
    {
        return $this->belongsTo(Clase::class, 'clase_id', 'id');
    }
}
