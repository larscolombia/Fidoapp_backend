<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['about_self', 'expert','description', 'facebook_link',
     'instagram_link', 'twitter_link', 'dribbble_link','tags','pdf','professional_title',
     'validation_number','speciality_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function speciality()
    {
        return $this->belongsTo(Speciality::class,'speciality_id','id');
    }
}
