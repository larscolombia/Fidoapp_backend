<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EBook extends Model
{
    use HasFactory;

    protected $table = 'e_book';
    protected $guarded = [];

    public function book_ratings()
    {
        return $this->hasMany(BookRating::class,'e_book_id','id');
    }
}
