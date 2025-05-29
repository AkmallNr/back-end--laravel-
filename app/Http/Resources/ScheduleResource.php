<?php

// app/Http/Resources/QuoteResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'notes' => $this->notes,
            'repeat' => $this->repeat,
            'day' => $this->day,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'userId' => $this->userId,
        ];
    }
}
