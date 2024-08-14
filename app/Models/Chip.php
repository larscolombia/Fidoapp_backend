<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;

class Chip extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_identificacion',
        'fecha_implantacion',
        'num_contacto',
        'fabricante_id',
        'pet_id',
    ];

    protected $dates = [
        'fecha_implantacion',
    ];

    protected $table = 'chips';

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class);
    }
}
