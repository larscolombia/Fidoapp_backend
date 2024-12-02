<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EBookUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function e_book()
    {
        return $this->belongsTo(EBook::class,'e_book_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
