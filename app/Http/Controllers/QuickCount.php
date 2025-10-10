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
        $candidates = Candidates::withCount('votes')
            ->orderBy('order_number')
            ->get();
    
        $totalVotes = $candidates->sum('votes_count');
    
        request()->merge(['total_votes' => $totalVotes]);
    
        return $this->successResponse(
            CandidateVoteResource::collection($candidates),
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
