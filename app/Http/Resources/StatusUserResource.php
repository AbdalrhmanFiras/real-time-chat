<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_online' => $this->is_online,
            'last_seen_at' => $this->when(!is_null($this->last_seen_at), $this->last_seen_at),

        ];
    }
}
