<?php

namespace Modules\Pet\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'pettype' => optional($this->pettype)->name,
            'breed' => optional($this->breed)->name,
            'breed_id' => $this->breed_id,
            'size' => $this->size,
            'pet_image' => $this->media->pluck('original_url')->first(),
            'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->format('Y-m-d') : null,
            'age' => $this->age,
            'gender' => $this->gender,
            'weight' => $this->weight ?? 0,
            'weight_unit' => $this->weight_unit ?? '',
            'height' => $this->height ?? 0,
            'height_unit' => $this->height_unit ?? '',
            'user_id' => $this->user_id,
            'permission_expiration' => $this->permission_pet_profile ? Carbon::parse($this->permission_pet_profile->expiration)->format('d-m-Y') : null,
            'status' => $this->status,
            'qr_code' => $this->qr_code,
            'passport' => $this->passport,
            'pet_fur' => !is_null($this->pet_fur) ? $this->pet_fur : __('pet.not specified'),
            'chip' => $this->chip,
            'description' => $this->additional_info,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y') : null,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
