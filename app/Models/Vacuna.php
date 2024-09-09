<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;


class Vacuna extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'vacunas';

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
