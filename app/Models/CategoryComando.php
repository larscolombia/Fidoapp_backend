<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryComando extends Model
{
    use HasFactory;

    protected $table = 'category_comandos';
    protected $guarded = [];

    public function comandos()
    {
        return $this->hasMany(Comando::class, 'category_id');
    }
}
