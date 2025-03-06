<?php

namespace App\Models;

use App\Models\PermissionProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionPetProfile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function permission_profile()
    {
        return $this->belongsTo(PermissionProfile::class,'permission_profile_id','id');
    }
}
