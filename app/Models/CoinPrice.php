<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPrice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }
}
