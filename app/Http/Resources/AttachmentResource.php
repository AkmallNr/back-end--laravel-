<?php

// app/Http/Resources/AttachmentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attachment' => $this->attachment,
        ];
    }
}

