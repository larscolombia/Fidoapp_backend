<?php

namespace App\Models;

use App\Models\CoursePlatformVideo;
use Modules\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CursoPlataforma extends Model
{
    use HasFactory;

    protected $table = 'courses_platform';
    protected $guarded = [];

    public function clases()
    {
        return $this->hasMany(Clase::class, 'course_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(CoursePlatformVideo::class, 'course_platform_id','id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id','id');
    }
}
