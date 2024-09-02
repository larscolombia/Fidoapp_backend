<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    protected $table = 'clases';
    protected $guarded = [];

    public function ejercicios()
    {
        return $this->hasMany(Ejercicio::class);
    }

    public function cursoPlataforma()
    {
        return $this->belongsTo(CursoPlataforma::class, 'course_id', 'id');
    }
}
