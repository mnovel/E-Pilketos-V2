<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeBallotBox extends Model
{
    /** @use HasFactory<\Database\Factories\BarcodeBallotBoxFactory> */
    use HasFactory, HasUuids;

    protected $table = 'barcode_ballot_boxes';
    protected $primaryKey = 'device_id';

    protected $fillable = [
        'device_id',
        'token',
        'participant_id',
    ];

    protected $casts = [
        'device_id' => 'string',
        'token' => 'string',
        'participant_id' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function participant()
    {
        return $this->belongsTo(Participants::class);
    }
}
