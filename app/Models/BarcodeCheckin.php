<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeCheckin extends Model
{
    /** @use HasFactory<\Database\Factories\BarcodeCheckinFactory> */
    use HasFactory, HasUuids;

    protected $table = 'barcode_checkins';
    protected $primaryKey = 'device_id';

    protected $fillable = [
        'device_id',
        'token',
    ];

    protected $casts = [
        'device_id' => 'string',
        'token' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
