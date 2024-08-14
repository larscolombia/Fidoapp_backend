<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabricante extends Model
{
    use HasFactory;

    protected $table = 'fabricantes';

    protected $fillable = [
        'nombre',
    ];

    public function chips()
    {
        return $this->hasMany(Chip::class);
    }
}
