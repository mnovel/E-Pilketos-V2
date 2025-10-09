<?php

namespace App\Http\Controllers;

use App\Models\Participants;
use App\Http\Requests\StoreParticipantsRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Http\Resources\ParticipantDetailResource;
use App\Http\Resources\ParticipantResource;
use App\Models\Classes;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantsController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Participants::with(['user', 'class'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'voter');
            });

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $participantsPaginator = $query->paginate(10);

        $meta = [
            'current_page' => $participantsPaginator->currentPage(),
            'last_page'    => $participantsPaginator->lastPage(),
            'per_page'     => $participantsPaginator->perPage(),
            'total'        => $participantsPaginator->total(),
        ];

        $participants = ParticipantResource::collection($participantsPaginator);

        return $this->successResponse(
            [
                'participants' => $participants,
                'meta' => $meta
            ],
            'Participants retrieved successfully'
        );
    }


    public function show(Participants $participant)
    {
        return $this->successResponse(new ParticipantDetailResource($participant), 'Participant retrieved successfully');
    }


    public function store(StoreParticipantsRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $class = Classes::findOrFail($validated['participant']['class_id']);

            if ($class->participants()->count() >= $class->max_users) {
                return $this->errorResponse(null, 'Class is full');
            }

            $user = User::create($validated['user']);

            $participant = $user->participant()->create($validated['participant']);

            DB::commit();

            return $this->successResponse(
                new ParticipantResource($participant),
                'Participant created successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create participant: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateParticipantsRequest $request, Participants $participant)
    {
        DB::beginTransaction();
        try {
            if ($request->has('user')) {
                $participant->user->update($request->validated()['user']);
                $status = $request->validated()['user']['status'];
                if ($status !== 'active') {
                    $participant->voting_status = null;
                    $participant->save();
                    $participant->votes()->delete();
                }
            }

            if ($request->has('participant')) {
                $participant->update($request->validated()['participant']);
                $is_reset = $request->validated()['participant']['is_reset'];
                if ($is_reset) {
                    $participant->voting_status = null;
                    $participant->save();
                    $participant->votes()->delete();
                }
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
