<?php

namespace App\Http\Controllers;

use App\Models\Participants;
use App\Http\Requests\StoreParticipantsRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Http\Resources\ParticipantDetailResource;
use App\Http\Resources\ParticipantResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class ParticipantsController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $participants = Participants::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'voter');
            })
            ->get();
        return $this->successResponse(ParticipantResource::collection($participants), 'Participants retrieved successfully');
    }

    public function show(Participants $participant)
    {
        return $this->successResponse(new ParticipantDetailResource($participant), 'Participant retrieved successfully');
    }


    public function store(StoreParticipantsRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->validated()['user']);
            $participant = $user->participant()->create($request->validated()['participant']);
            DB::commit();
            return $this->successResponse(new ParticipantResource($participant), 'Participant created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create participant: ' . $e->getMessage());
        }
    }

    public function update(UpdateParticipantsRequest $request, Participants $participant)
    {
        DB::beginTransaction();

        try {
            if ($request->has('user')) {
                $participant->user->update($request->validated()['user']);
            }

            if ($request->has('participant')) {
                $participant->update($request->validated()['participant']);
            }

            DB::commit();

            return $this->successResponse(
                new ParticipantResource($participant->fresh()),
                'Participant updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update participant: ' . $e->getMessage());
        }
    }


    public function destroy(Participants $participant)
    {
        DB::beginTransaction();
        try {
            $participant->user->delete();
            $participant->delete();
            DB::commit();
            return $this->successResponse(null, 'Participant deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete participant: ' . $e->getMessage());
        }
    }
}
