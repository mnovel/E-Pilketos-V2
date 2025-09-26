<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionSessions extends Model
{
    /** @use HasFactory<\Database\Factories\ElectionSessionsFactory> */
    use HasFactory, HasUuids;

    protected $table = 'election_sessions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'string'
    ];
}
