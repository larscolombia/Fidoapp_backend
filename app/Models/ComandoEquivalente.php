<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandoEquivalente extends Model
{
    use HasFactory;

    protected $table = 'comando_equivalente';
    protected $fillable = ['comando_id', 'name', 'user_id'];

    public function comando()
    {
        return $this->belongsTo(Comando::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
