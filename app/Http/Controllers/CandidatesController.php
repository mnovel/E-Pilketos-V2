<?php

namespace App\Http\Controllers;

use App\Models\Candidates;
use App\Http\Requests\StoreCandidatesRequest;
use App\Http\Requests\UpdateCandidatesRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class CandidatesController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $candidates = Candidates::all();
        return $this->successResponse($candidates, 'Candidates retrieved successfully');
    }

    public function show(Candidates $candidate)
    {
        return $this->successResponse($candidate, 'Candidate details retrieved successfully');
    }

    public function store(StoreCandidatesRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $fileName = "candidate_{$data['order_number']}." . $ext;
            $data['photo'] = $request->file('photo')->storeAs('candidates', $fileName, 'public');
        }

        $candidate = Candidates::create($data);

        return $this->successResponse($candidate, 'Candidate created successfully', 201);
    }

    public function update(UpdateCandidatesRequest $request, Candidates $candidate)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {

            if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
                Storage::disk('public')->delete($candidate->photo);
            }

            $ext = $request->file('photo')->getClientOriginalExtension();
            $fileName = "candidate_{$data['order_number']}." . $ext;
            $data['photo'] = $request->file('photo')->storeAs('candidates', $fileName, 'public');
        }

        $candidate->update($data);

        return $this->successResponse($candidate, 'Candidate updated successfully');
    }

    public function destroy(Candidates $candidate)
    {

        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();
        return $this->successResponse(null, 'Candidate deleted successfully');
    }
}
