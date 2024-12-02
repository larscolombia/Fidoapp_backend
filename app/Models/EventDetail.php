<?php

namespace App\Models;

use Modules\Pet\Models\Pet;
use Modules\Event\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class,'pet_id','id');
    }
}
