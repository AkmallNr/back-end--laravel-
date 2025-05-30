<?php

// app/Http/Resources/QuoteResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'userId' => $this->userId,
        ];
    }
}
