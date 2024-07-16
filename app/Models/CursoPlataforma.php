<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoPlataforma extends Model
{
    use HasFactory;

    protected $table = 'courses_platform';
    protected $guarded = [];

    public function clases()
    {
        return $this->hasMany(Clase::class, 'course_id', 'id');
    }
}
