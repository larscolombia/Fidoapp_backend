<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;

class Diario extends Model
{
    use HasFactory;

    protected $table = 'diarios';
    protected $guarded = [];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
