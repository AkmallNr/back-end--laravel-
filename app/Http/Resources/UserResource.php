<?php

// app/Http/Resources/UserResource.php

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
            'quotes' => QuoteResource::collection($this->quotes),
            'groups' => GroupResource::collection($this->groups),
        ];
    }
}

