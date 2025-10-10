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
        $totalVotes = $request->total_votes ?? 0;

        $percentage = $totalVotes > 0
            ? round(($this->votes_count / $totalVotes) * 100, 2)
            : 0;

        return [
            'candidate_id'   => $this->id,
            'name'           => $this->name,
            'photo'          => $this->photo ? secure_asset('storage/' . $this->photo) : null,
            'order_number'   => $this->order_number,
            'quick_count'    => [
                'total_votes'  => (int) $this->votes_count,
                'percentage'   => (float) $percentage,
            ]
        ];
    }
}
