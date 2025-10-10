<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateDetailResource extends JsonResource
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
            'order_number' => $this->order_number,
            'name' => $this->name,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'vision' => $this->vision,
            'mission' => $this->mission,
            'featured_program' => $this->featured_program,
        ];
    }
}
