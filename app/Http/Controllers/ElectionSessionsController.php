<?php

namespace App\Http\Controllers;

use App\Models\ElectionSessions;
use App\Http\Requests\StoreElectionSessionsRequest;
use App\Http\Requests\UpdateElectionSessionsRequest;
use App\Traits\ApiResponse;

class ElectionSessionsController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $sessions = ElectionSessions::all();
        return $this->successResponse($sessions, 'Election sessions fetched successfully');
    }

    public function show(ElectionSessions $electionSession)
    {
        return $this->successResponse($electionSession, 'Election session details');
    }

    public function store(StoreElectionSessionsRequest $request)
    {
        $session = ElectionSessions::create($request->validated());
        return $this->successResponse($session, 'Election session created', 201);
    }

    public function update(UpdateElectionSessionsRequest $request, ElectionSessions $electionSession)
    {
        $electionSession->update($request->validated());
        return $this->successResponse($electionSession, 'Election session updated');
    }

    public function destroy(ElectionSessions $electionSession)
    {
        $electionSession->delete();
        return $this->successResponse(null, 'Election session deleted');
    }
}
