<?php

// app/Http/Resources/ProjectResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'groupId' => $this->groupId,
            'tasks' => TaskResource::collection($this->tasks),
        ];
    }
}
