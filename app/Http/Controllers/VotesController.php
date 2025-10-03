<?php

namespace App\Http\Controllers;

use App\Models\Votes;
use App\Http\Requests\StoreVotesRequest;
use App\Traits\ApiResponse;

class VotesController extends Controller
{
    use ApiResponse;

    public function store(StoreVotesRequest $request)
    {
        $vote = Votes::create($request->validated());
        return $this->successResponse($vote, 'Vote recorded successfully');
    }
}
