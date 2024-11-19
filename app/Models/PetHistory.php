<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vacuna;
use Modules\Pet\Models\Pet;
use App\Models\Antigarrapata;
use App\Models\Antidesparasitante;
use Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PetHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'pet_histories';

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian()
    {
        return $this->belongsTo(User::class);
    }

    public function category_rel()
    {
        return $this->belongsTo(Category::class,'category','id');
    }

    public function vacuna()
    {
        return $this->belongsTo(Vacuna::class, 'vacuna_id','id');
    }

    public function antiparasitante()
    {
        return $this->belongsTo(Antidesparasitante::class, 'antidesparasitante_id','id');
    }

    public function antigarrapata()
    {
        return $this->belongsTo(Antigarrapata::class, 'antigarrapata_id','id');
    }
}
