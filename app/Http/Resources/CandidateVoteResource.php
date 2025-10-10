<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateVoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'candidate_id'   => $this->candidate_id,
            'name'           => $this->name,
            'photo'          => $this->photo ? secure_asset('storage/' . $this->photo) : null,
            'order_number'   => $this->order_number,
            'quick_count'    => [
                'total_votes'    => (int) $this->total_votes,
                'percentage'     => (float) $this->percentage,
            ]
        ];
    }
}
