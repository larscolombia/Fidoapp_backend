<?php

namespace Modules\Event\Models;

use App\Models\User;
use App\Models\BaseModel;
use App\Models\EventDetail;
use App\Models\CalendarEvent;
use App\Models\Traits\HasSlug;
use Modules\Booking\Models\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;


    protected $table = 'events';
    protected $fillable = ['name', 'date', 'end_date', 'tipo','event_time', 'user_id', 'location', 'description', 'status', 'image'];
    protected $appends = ['event_image'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Event\database\factories\EventFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    protected function getEventImageAttribute()
    {
        $media = $this->getFirstMediaUrl('event_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

    public function scopeWithDistance($query, $latitude, $longitude)
    {
        $unit_value = 6371; // Earth's radius in kilometers, you can change this if you want miles

        return $query
            ->selectRaw("*, (
            {$unit_value} * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance", [$latitude, $longitude, $latitude])
            ->orderBy('distance', 'asc');
    }

    public function detailEvent()
    {
        return $this->hasMany(EventDetail::class,'event_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class,'event_id','id');
    }
}
