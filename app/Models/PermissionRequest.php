<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

      // Relación con el usuario que envía la solicitud
      public function requester()
      {
          return $this->belongsTo(User::class, 'requester_id');
      }

      // Relación con el usuario que recibe la solicitud
      public function target()
      {
          return $this->belongsTo(User::class, 'target_id');
      }
}
