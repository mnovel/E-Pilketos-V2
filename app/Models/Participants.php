<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participants extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantsFactory> */
    use HasFactory, HasUuids;

    protected $table = 'participants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nisn',
        'voting_status',
        'user_id',
        'class_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function barcodeCheckins()
    {
        return $this->hasMany(BarcodeCheckin::class);
    }
}
