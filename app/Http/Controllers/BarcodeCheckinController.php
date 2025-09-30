<?php

namespace App\Http\Controllers;

use App\Models\BarcodeCheckin;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeCheckinController extends Controller
{
    use ApiResponse;

    public function listActiveDevice()
    {
        $activeDevices = BarcodeCheckin::latest()->get();
        return $this->successResponse($activeDevices, 'Active devices retrieved successfully');
    }

    public function generateDeviceId()
    {
        $deviceId = Str::uuid()->toString();
        $token = bin2hex(random_bytes(16));

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
            return $this->errorResponse('Barcode check-in not found', 404);
        }

        $barcodeCheckin->update([
            'token' => bin2hex(random_bytes(16))
        ]);

        $barcodeData = "{$barcodeCheckin->device_id}|{$barcodeCheckin->token}";
        $barcode = QrCode::format('png')->size(200)->generate($barcodeData);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        return $this->successResponse(
            ['barcode' => $barcodeBase64],
            'Barcode generated successfully'
        );
    }
}
