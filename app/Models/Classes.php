<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    /** @use HasFactory<\Database\Factories\ClassesFactory> */
    use HasFactory, HasUuids;

    protected $table = 'classes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'max_users',
        'election_session_id',
    ];

    protected $casts = [
        'name' => 'string',
        'max_users' => 'integer',
        'election_session_id' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function electionSession()
    {
        return $this->belongsTo(ElectionSessions::class, 'election_session_id');
    }

    public function participants()
    {
        return $this->hasMany(Participants::class, 'class_id');
    }

    protected static function booted()
    {
        static::deleting(function ($class) {
            foreach ($class->participants as $participant) {
                $participant->delete();
            }
        });
    }
}
