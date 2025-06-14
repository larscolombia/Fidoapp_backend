<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookRating extends Model
{
    use HasFactory;
    protected $table = 'book_ratings';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ebook()
    {
        return $this->belongsTo(EBook::class,'e_book_id','id');
    }
}
