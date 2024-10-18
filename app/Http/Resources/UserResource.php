<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'Nombre' => $this->name,
            'Email' => $this->email,
            'Rol' => "ID: {$this->rol->id} - {$this->rol->name}",
            'Puesto' => " ID:{$this->position->id} - {$this->position->name}",
        ];
    }
}
