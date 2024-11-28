<?php

namespace App\Models;

use Modules\Blog\Models\Blog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogRating extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function blog(){
        return $this->belongsTo(Blog::class,'blog_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
