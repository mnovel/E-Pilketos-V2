<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Votes extends Model
{
    /** @use HasFactory<\Database\Factories\VotesFactory> */
    use HasFactory, HasUuids;

    protected $table = 'votes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'candidate_id',
        'participant_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidates::class, 'candidate_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participants::class, 'participant_id');
    }
}
