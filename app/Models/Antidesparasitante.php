<?php

namespace App\Models;

use Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Antidesparasitante extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'antidesparasitantes';
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
