<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidates extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'candidates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'vision',
        'mission',
        'featured_program',
        'order_number',
        'photo',
    ];

    protected $casts = [
        'name' => 'string',
        'vision' => 'string',
        'mission' => 'string',
        'featured_program' => 'string',
        'order_number'  => 'integer',
    ];
}
