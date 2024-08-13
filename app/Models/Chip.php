<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chip extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_identificacion',
        'fecha_implantacion',
        'nombre_fabricante',
        'num_contacto',
    ];

    protected $dates = [
        'fecha_implantacion',
    ];

    protected $table = 'chips';
}
