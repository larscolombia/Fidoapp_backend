<?php

namespace App\Http\Resources\Comandos;

use Illuminate\Http\Resources\Json\JsonResource;

class ComandoEquivalenteResource extends JsonResource
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
            'comando_id' => $this->comando_id,
            'name' => $this->name,
            'user_name' => $this->user->name, // Suponiendo que el modelo User tiene un campo 'name'
            'comando_name' => $this->comando->name, // Suponiendo que el modelo Comando tiene un campo 'name'
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
