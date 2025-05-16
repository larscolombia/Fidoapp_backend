<?php

namespace Modules\Blog\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'blogs';
    protected $appends = ['blog_image'];
    protected $fillable = ['name','platform_id', 'tags', 'description','video','url', 'status','visualizations','duration'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Blog\database\factories\BlogFactory::new();
    }

    protected function getBlogImageAttribute()
    {
        $media = $this->getFirstMediaUrl('blog_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
}
