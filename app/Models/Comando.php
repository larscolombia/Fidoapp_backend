<?php

namespace App\Models;

use Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comando extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'comandos';

    public function category()
    {
        return $this->belongsTo(CategoryComando::class, 'category_id');
    }

    public function comandos_equivalentes()
    {
        return $this->hasMany(ComandoEquivalente::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
