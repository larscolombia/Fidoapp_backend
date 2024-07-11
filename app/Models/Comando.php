<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comando extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'comandos';
    
    public function category()
    {
        return $this->belongsTo(CategoryComando::class, 'category_id');
    }
}
