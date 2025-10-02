<?php

namespace App\Http\Controllers;

use App\Models\BarcodeBallotBox;
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

    public function generateBarcodeCheckin($deviceId)
    {
        $barcodeBallotBox = BarcodeBallotBox::where('device_id', $deviceId)->first();

        if (!$barcodeBallotBox) {
            return $this->errorResponse('Barcode Ballot Box not found');
        }

        $barcodeBallotBox->update([
            'token' => bin2hex(random_bytes(8))
        ]);

        $barcodeData = "{$barcodeBallotBox->device_id}|{$barcodeBallotBox->token}";
        $barcode = QrCode::format('png')->size(200)->generate($barcodeData);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        return $this->successResponse(
            ['barcode' => $barcodeBase64],
            'Barcode generated successfully'
        );
    }
}
