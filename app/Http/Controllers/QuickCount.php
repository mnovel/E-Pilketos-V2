<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidateVoteByClassResource;
use App\Http\Resources\CandidateVoteResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Candidates;
use App\Models\Classes;

class QuickCount extends Controller
{
    use ApiResponse;

    public function AllCount()
    {
        $vote = Candidates::withCount('votes')
            ->orderBy('order_number')
            ->get();

        $totalVotes = $vote->sum('votes_count');

        $vote = $vote->map(function ($item) use ($totalVotes) {
            $item->percentage = $totalVotes > 0
                ? round(($item->votes_count / $totalVotes) * 100, 2)
                : 0;

            return (object) [
                'candidate_id' => $item->id,
                'name'         => $item->name,
                'photo'        => $item->photo ? asset('storage/'.$item->photo) : '',
                'order_number' => $item->order_number,
                'total_votes'  => $item->votes_count,
                'percentage'   => $item->percentage,
            ];
        });

        return $this->successResponse(
            CandidateVoteResource::collection($vote),
            'Quick count retrieved successfully'
        );
    }

    public function CountByClass()
    {
        $classes = Classes::with([
            'participants.votes.candidate'
        ])->get();

        return $this->successResponse(
            CandidateVoteByClassResource::collection($classes),
            'Quick count by participant class retrieved successfully'
        );
    }
}
