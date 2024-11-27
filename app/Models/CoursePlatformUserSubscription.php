<?php

namespace App\Models;

use App\Models\User;
use App\Models\CursoPlataforma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoursePlatformUserSubscription extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function course_platform()
    {
        return $this->belongsTo(CursoPlataforma::class,'course_platform_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
