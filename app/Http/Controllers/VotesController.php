<?php

namespace App\Http\Controllers;

use App\Models\Votes;
use App\Http\Requests\StoreVotesRequest;
use App\Models\BarcodeBallotBox;
use App\Models\Participants;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class VotesController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:voter management')->only('store');
    }

    public function store(StoreVotesRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $participant = Participants::find($validated['participant_id']);
            $device = $validated['device_id'];

            if ($participant->voting_status != 'in_progress') {
                DB::rollBack();
                return $this->errorResponse(null, 'Participant is not allowed to vote, voting status is ' . $participant->voting_status);
            }

            $vote = Votes::create($validated);
            $participant->voting_status = 'completed';
            $participant->save();

            $balotBox = BarcodeBallotBox::where('device_id', $device)->first();
            $balotBox->participant_id = null;
            $balotBox->save();

            DB::commit();
            return $this->successResponse($vote, 'Vote recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(null, 'Failed to record vote: ' . $e->getMessage());
        }
    }
}
