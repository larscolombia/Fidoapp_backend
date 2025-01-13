<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'user_role' => $this->getRoleNames() ?? [],
            'api_token' => $this->api_token,
            'profile_image' => $this->avatar,
            'user_type' => $this->user_type,
            'login_type' => $this->login_type,
            'gender' => $this->gender,
            'address' => $this->address,
            'player_id' => $this->player_id,
            'profile_image' => $this->media->pluck('original_url')->first(),
            'device_token' => $this->device_token,
        ];
    }
}
