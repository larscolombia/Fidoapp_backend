<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'name',
        'parent_id',
        'status',
    ];

    public function diario()
    {
        return $this->hasMany(Diario::class, 'id', 'category_id');
    }
}
