<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'max_users' => $this->max_users,
            'election_session' => [
                'election_session_id'   => optional($this->electionSession)->id,
                'name'                  => optional($this->electionSession)->name,
                'status'                => optional($this->electionSession)->status,
            ]
        ];
    }
}
