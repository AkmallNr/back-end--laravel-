<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'deadline' => $this->deadline,
            'reminder' => $this->reminder,
            'priority' => $this->priority,
            'status' => $this->status,
            'quoteId' => is_string($this->quoteId) || is_numeric($this->quoteId)
                ? $this->quoteId
                : (is_array($this->quoteId) ? $this->quoteId : json_decode($this->quoteId, true)),
            'attachment' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return is_string($attachment->file_url)
                        ? $attachment->file_url
                        : (is_array($attachment->file_url)
                            ? $attachment->file_url
                            : json_decode($attachment->file_url, true));
                })->toArray();
            }, []),
            'completed_at' => $this->completed_at ? $this->completed_at->toDateTimeString() : null,
        ];
    }
}
