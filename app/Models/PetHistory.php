<?php

namespace App\Models;

use App\Models\User;
use Modules\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PetHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'pet_histories';

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian()
    {
        return $this->belongsTo(User::class);
    }
}
