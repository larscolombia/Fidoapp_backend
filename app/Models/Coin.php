<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function coinPrice()
    {
        return $this->hasOne(CoinPrice::class);
    }
}
