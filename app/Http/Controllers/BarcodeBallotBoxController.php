<?php

namespace App\Http\Controllers;

use App\Http\Resources\BarcodeBallotBoxResource;
use App\Models\BarcodeBallotBox;
use App\Models\ElectionSessions;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeBallotBoxController extends Controller
{
    use ApiResponse;

    public function listActiveDevice($deviceId = null)
    {
        $activeDevices = BarcodeBallotBox::query();

        if ($deviceId) {
            $activeDevices->where('device_id', $deviceId);
        }

        $activeDevices = $activeDevices->get();
        return $this->successResponse($activeDevices, 'Active devices retrieved successfully');
    }

    public function generateDeviceId()
    {
        $deviceId = Str::uuid()->toString();
        $token = bin2hex(random_bytes(8));

        $barcodeBallotBox = BarcodeBallotBox::create([
            'device_id' => $deviceId,
            'token'     => $token,
        ]);

        return $this->successResponse(
            ['device_id' => $barcodeBallotBox->device_id, 'token' => $barcodeBallotBox->token],
            'Device ID generated successfully'
        );
    }

    public function generateBarcodeBallotBox($deviceId)
    {
        $barcodeBallotBox = BarcodeBallotBox::where('device_id', $deviceId)->first();

        if (!$barcodeBallotBox) {
            return $this->errorResponse('Barcode Ballot Box not found');
        }

        $barcodeBallotBox->update([
            'token' => bin2hex(random_bytes(8)),
            'participant_id' => null,
        ]);
        $barcodeBallotBox->save();

        $barcodeData = "{$barcodeBallotBox->device_id}|{$barcodeBallotBox->token}";
        $barcode = QrCode::format('png')->size(200)->generate($barcodeData);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        $now = now();
        $activeSession = ElectionSessions::with('class')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        $classes = $activeSession ? $activeSession->class : collect([]);

        return $this->successResponse(
            new BarcodeBallotBoxResource([
                'classes'   => $classes,
                'session'   => $activeSession,
                'barcode'   => $barcodeBase64
            ]),
            'Barcode Ballot Box generated successfully'
        );
    }


    public function deleteDeviceId($deviceId)
    {
        $barcodeBallotBox = BarcodeBallotBox::where('device_id', $deviceId)->first();

        if (!$barcodeBallotBox) {
            return $this->errorResponse('Device ID not found');
        }

        $barcodeBallotBox->delete();

        return $this->successResponse(null, 'Device ID deleted successfully');
    }
}
