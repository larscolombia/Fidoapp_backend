<?php

namespace App\Models;

use App\Models\CursoPlataforma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoursePlatformVideo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function coursePlatform()
    {
        return $this->belongsTo(CursoPlataforma::class);
    }
}
