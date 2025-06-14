<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Pet\Models\Pet;

class SpecialCondition extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function pet()
    {
        return $this->BelongsTo(Pet::class);
    }
}
