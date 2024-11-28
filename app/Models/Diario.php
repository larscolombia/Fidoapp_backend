<?php

namespace App\Models;

use Modules\Pet\Models\Pet;
use Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diario extends Model
{
    use HasFactory;

    protected $table = 'diarios';
    protected $guarded = [];
    protected $appends = ['image'];
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
    public function getImageAttribute()
    {
        return asset($this->attributes['image']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
