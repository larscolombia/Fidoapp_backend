<?php

namespace App\Models;

use Modules\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'user_notifications';

    public function bookings()
    {
        return $this->belongsTo(Booking::class,'booking_id','id');
    }
}
