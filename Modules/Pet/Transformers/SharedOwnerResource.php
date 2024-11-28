<?php

namespace Modules\Pet\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Pet\Transformers\PetResource;

class SharedOwnerResource extends JsonResource
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
            'email' => $this->email,
            'full_name' => $this->full_name,
            'profile_image' => $this->media->pluck('original_url')->first(),
        ];
    }
}
