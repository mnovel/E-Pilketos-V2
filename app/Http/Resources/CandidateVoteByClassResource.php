<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateVoteByClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $participants = ($this->participants ?? collect())
            ->filter(fn($p) => optional($p->user)->status === 'active');

        $totalParticipants = $participants->count();

        $voted = $participants->filter(fn($p) => $p->votes->isNotEmpty());
        $notVoted = $participants->filter(fn($p) => $p->votes->isEmpty());

        $votedCount = $voted->count();
        $notVotedCount = $notVoted->count();

        $voteCounts = $voted->flatMap(fn($p) => $p->votes)
            ->groupBy('candidate_id')
            ->map(fn($votes) => $votes->count());

        $candidates = \App\Models\Candidates::orderBy('order_number')->get();

        $candidateData = $candidates->map(function ($candidate) use ($voteCounts, $votedCount) {
            $totalVotes = $voteCounts[$candidate->id] ?? 0;
            $percentage = $votedCount > 0
                ? round(($totalVotes / $votedCount) * 100, 2)
                : 0;

            return [
                'id'           => $candidate->id,
                'name'         => $candidate->name,
                'photo'        => $candidate->photo ? asset('storage/' . $candidate->photo) : null,
                'order_number' => (int) $candidate->order_number,
                'total_votes'  => (int) $totalVotes,
                'percentage'   => $percentage,
            ];
        });

        return [
            'class_id'           => $this->id,
            'class_name'         => $this->name,
            'total_participants' => $totalParticipants,
            'voted_count'        => $votedCount,
            'not_voted_count'    => $notVotedCount,
            'candidates'         => $candidateData,
        ];
    }
}
