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
        'nis',
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

    public function BarcodeBallotBox()
    {
        return $this->hasMany(BarcodeBallotBox::class, 'participant_id');
    }

    public function votes()
    {
        return $this->hasMany(Votes::class, 'participant_id');
    }

    protected static function booted()
    {
        static::deleting(function ($participant) {
            $participant->BarcodeBallotBox()->delete();
            $participant->votes()->delete();

            if ($participant->user) {
                $participant->user->delete();
            }
        });
    }
}
