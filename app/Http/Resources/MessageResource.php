<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message' => $this->message,
            'sender' => new AuthResource($this->whenLoaded('Sender')),
            'receiver' => new AuthResource($this->whenLoaded('Receiver')),
            'file_path' => $this->when(!is_null($this->file), $this->file),
            'read_at' => $this->when(!is_null($this->read_at), $this->read_at)
        ];
    }
}
