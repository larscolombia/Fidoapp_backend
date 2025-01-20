<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class,'id','speciality_id');
    }
}
