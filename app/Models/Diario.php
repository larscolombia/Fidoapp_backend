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
    protected $appends = ['image'];
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
    public function getImageAttribute()
    {
        return asset($this->attributes['image']);
    }
}
