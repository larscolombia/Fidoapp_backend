<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;

class ActivityLevel extends Model
{
    use HasFactory;

    protected $table = 'activity_levels';
    protected $fillable = [
        'pet_id',
        'daily_steps',
        'distance_covered',
        'calories_burned',
        'active_minutes',
        'goal_steps',
        'goal_distance',
        'goal_calories',
        'goal_active_minutes',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
