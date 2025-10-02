<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanBallotBoxRequest;
use App\Http\Requests\ScanCheckinRequest;
use App\Models\BarcodeBallotBox;
use App\Models\BarcodeCheckin;
use App\Models\Participants;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    use ApiResponse;

    public function scanCheckin(ScanCheckinRequest $request)
    {
        $data = $request->validated();

        $parts = explode('|', $data['barcode_data']);

        if (count($parts) !== 2) {
            return $this->errorResponse(null, 'Invalid barcode format');
        }

        [$deviceId, $token] = $parts;
        $participantId = $data['participant_id'];

        $checkin = BarcodeCheckin::where('device_id', $deviceId)
            ->where('token', $token)
            ->first();

        if (!$checkin) {
            return $this->errorResponse(null, 'Invalid barcode data');
        }

        $participant = Participants::find($participantId);
        if (!$participant) {
            return $this->errorResponse(null, 'Participant not found');
        }

        if ($participant->voting_status !== null) {
            return $this->errorResponse(null, 'Participant already checked in');
        }

        $participant->update([
            'voting_status' => 'waiting',
        ]);

        return $this->successResponse(null, 'Check-in successful');
    }

    public function scanBallotBox(ScanBallotBoxRequest $request)
    {
        $data = $request->validated();

        $parts = explode('|', $data['barcode_data']);

        if (count($parts) !== 2) {
            return $this->errorResponse(null, 'Invalid barcode format');
        }

        [$deviceId, $token] = $parts;

        $ballotBox = BarcodeBallotBox::where('device_id', $deviceId)
            ->where('token', $token)
            ->first();

        if (!$ballotBox) {
            return $this->errorResponse(null, 'Invalid barcode data');
        }

        $participant = Participants::find($data['participant_id']);
        if (!$participant) {
            return $this->errorResponse(null, 'Participant not found');
        }

        if ($participant->voting_status !== 'waiting') {
            return $this->errorResponse(null, 'Participant not eligible to vote');
        }

        $ballotBox->update([
            'participant_id' => $participant->id,
        ]);

        $participant->update([
            'voting_status' => 'in_progress',
        ]);

        return $this->successResponse(null, 'Ballot box scan successful');
    }
}
