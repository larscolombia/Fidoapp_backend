<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingRequestMapping extends Model
{
    use HasFactory;


    protected $table = 'booking_request_mapping';
    
    protected $fillable = ['booking_id', 'walker_id', 'status'];

    
    protected static function newFactory()
    {
        return \Modules\Booking\Database\factories\BookingRequestMappingFactory::new();
    }
}
