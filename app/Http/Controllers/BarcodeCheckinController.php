<?php

namespace App\Http\Controllers;

use App\Http\Resources\BarcodeCheckinResource;
use App\Models\BarcodeCheckin;
use App\Models\ElectionSessions;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeCheckinController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:voter management');
    }

    public function listActiveDevice()
    {
        $activeDevices = BarcodeCheckin::latest()->get();
        return $this->successResponse($activeDevices, 'Active devices retrieved successfully');
    }

    public function generateDeviceId()
    {
        $deviceId = Str::uuid()->toString();
        $token = bin2hex(random_bytes(8));

        $barcodeCheckin = BarcodeCheckin::create([
            'device_id' => $deviceId,
            'token'     => $token,
        ]);

        return $this->successResponse(
            ['device_id' => $barcodeCheckin->device_id, 'token' => $barcodeCheckin->token],
            'Device ID generated successfully'
        );
    }

    public function generateBarcodeCheckin($deviceId)
    {
        $barcodeCheckin = BarcodeCheckin::where('device_id', $deviceId)->first();

        if (!$barcodeCheckin) {
            return $this->errorResponse(null, 'Barcode check-in not found');
        }

        $barcodeCheckin->update([
            'token' => bin2hex(random_bytes(8))
        ]);
        $barcodeCheckin->save();

        $barcodeData = "{$barcodeCheckin->device_id}|{$barcodeCheckin->token}";
        $barcode = QrCode::format('png')->size(200)->generate($barcodeData);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        $now = now();
        $activeSession = ElectionSessions::with('class')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();

        $classes = $activeSession ? $activeSession->class : collect([]);

        return $this->successResponse(
            new BarcodeCheckinResource([
                'classes'   => $classes,
                'session'   => $activeSession,
                'barcode'   => $barcodeBase64
            ]),
            'Barcode Checkin generated successfully'
        );
    }

    public function deleteDeviceId($deviceId)
    {
        $barcodeCheckin = BarcodeCheckin::where('device_id', $deviceId)->first();

        if (!$barcodeCheckin) {
            return $this->errorResponse(null, 'Device ID not found');
        }

        $barcodeCheckin->delete();

        return $this->successResponse(null, 'Device ID deleted successfully');
    }
}
