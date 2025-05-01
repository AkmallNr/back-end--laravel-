<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'groups' => GroupResource::collection($this->groups),
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'quotes' => QuoteResource::collection($this->quotes)
        ];
    }
}
