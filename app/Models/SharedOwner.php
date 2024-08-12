<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;

class SharedOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
    ];
    protected $table = 'shared_owners';

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }
}
