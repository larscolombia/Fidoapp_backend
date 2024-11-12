<?php

namespace Modules\Pet\Models;

use App\Models\Chip;
use App\Models\User;
use App\Models\Diario;
use App\Models\Vacuna;
use App\Models\BaseModel;
use App\Models\PetHistory;
use App\Models\ActivityLevel;
use App\Models\Traits\HasSlug;
use App\Models\Antidesparasitante;
use App\Models\Antigarrapata;
use Modules\Booking\Models\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;

    protected $table = 'pets';
    protected $fillable = ['name', 'slug', 'pettype_id', 'breed_id', 'date_of_birth', 'age', 'gender', 'weight', 'height', 'weight_unit',
    'height_unit', 'additional_info', 'user_id', 'status', 'qr_code'];
    protected $appends = ['pet_image','qr_code'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Pet\database\factories\PetFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sharedOwners()
    {
        return $this->belongsToMany(User::class, 'shared_owners', 'pet_id', 'user_id');
    }

    protected function getPetImageAttribute()
    {
        $media = $this->getFirstMediaUrl('pet_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

    public function getQrCodeAttribute()
    {
        return asset($this->attributes['qr_code']);
    }

    public function pettype()
    {
        return $this->belongsTo(PetType::class, 'pettype_id');
    }
    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id','id');
    }

    public function petnote()
    {
        return $this->hasMany(PetNote::class, 'pet_id')->with('createdBy');
    }

    public function diario()
    {
        return $this->hasMany(Diario::class, 'pet_id');
    }

    public function chip()
    {
        return $this->hasOne(Chip::class, 'pet_id');
    }

    public function activityLevel()
    {
        return $this->hasOne(ActivityLevel::class, 'pet_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'pet_id');
    }

    public function vacunas()
    {
        return $this->hasMany(Vacuna::class, 'pet_id');
    }

    public function antidesparasitantes()
    {
        return $this->hasMany(Antidesparasitante::class, 'pet_id');
    }

    public function antigarrapatas()
    {
        return $this->hasMany(Antigarrapata::class, 'pet_id');
    }


    public function histories()
    {
        return $this->hasMany(PetHistory::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
