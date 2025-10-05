<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Candidates;

class QuickCount extends Controller
{
    use ApiResponse;

    public function AllCount()
    {
        // Ambil semua kandidat beserta total suaranya
        $vote = \DB::table('candidates')
            ->leftJoin('votes', 'candidates.id', '=', 'votes.candidate_id')
            ->select(
                'candidates.id as candidate_id',
                'candidates.name',
                'candidates.photo',
                'candidates.order_number',
                \DB::raw('COUNT(votes.id) as total_votes')
            )
            ->groupBy(
                'candidates.id',
                'candidates.name',
                'candidates.photo',
                'candidates.order_number'
            )
            ->orderBy('candidates.order_number')
            ->get();

        $totalVotes = $vote->sum('total_votes');

        $vote = $vote->map(function ($item) use ($totalVotes) {
            $item->percentage = $totalVotes > 0
                ? round(($item->total_votes / $totalVotes) * 100, 2)
                : 0;

            return $item;
        });

        return $this->successResponse($vote, 'Quick count retrieved successfully');
    }


    public function CountByClass()
    {
        $data = \DB::table('classes')
            ->leftJoin('participants', 'participants.class_id', '=', 'classes.id')
            ->leftJoin('votes', 'participants.id', '=', 'votes.participant_id')
            ->leftJoin('candidates', 'votes.candidate_id', '=', 'candidates.id')
            ->select(
                'classes.id as class_id',
                'classes.name as class_name',
                'candidates.id as candidate_id',
                'candidates.name as candidate_name',
                'candidates.photo',
                'candidates.order_number',
                \DB::raw('COUNT(votes.id) as total_votes'),
                \DB::raw('COUNT(DISTINCT participants.id) as total_participants'),
                \DB::raw('COUNT(DISTINCT CASE WHEN votes.id IS NOT NULL THEN participants.id END) as voted_count'),
                \DB::raw('COUNT(DISTINCT CASE WHEN votes.id IS NULL THEN participants.id END) as not_voted_count')
            )
            ->groupBy(
                'classes.id',
                'classes.name',
                'candidates.id',
                'candidates.name',
                'candidates.photo',
                'candidates.order_number'
            )
            ->orderBy('classes.name')
            ->orderBy('candidates.order_number')
            ->get();

        $grouped = $data->groupBy('class_id')->map(function ($items) {
            $first = $items->first();

            return [
                'class_id' => $first->class_id,
                'class_name' => $first->class_name,
                'total_participants' => (int) $first->total_participants,
                'voted_count' => (int) $first->voted_count,
                'not_voted_count' => (int) $first->not_voted_count,
                'candidates' => $items->map(function ($item) use ($first) {
                    $percentage = $first->voted_count > 0
                        ? round(($item->total_votes / $first->voted_count) * 100, 2)
                        : 0;

                    return [
                        'id' => $item->candidate_id,
                        'name' => $item->candidate_name,
                        'photo' => $item->photo,
                        'order_number' => (int) $item->order_number,
                        'total_votes' => (int) $item->total_votes,
                        'percentage' => $percentage,
                    ];
                })->values(),
            ];
        })->values();

        return $this->successResponse($grouped, 'Quick count by participant class retrieved successfully');
    }
}
