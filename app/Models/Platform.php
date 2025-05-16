<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Models\Blog;

class Platform extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function clases()
    {
        return $this->hasMany(Clase::class);
    }
}
