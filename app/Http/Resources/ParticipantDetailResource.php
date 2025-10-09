<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nis'          => $this->nis,
            'voting_status' => $this->voting_status,
            'user'          => [
                'user_id'   => optional($this->user)->id,
                'name'  => optional($this->user)->name,
                'email' => optional($this->user)->email,
                'role'  => optional($this->user)->role,
                'status' => optional($this->user)->status,
            ],
            'class' => [
                'class_id'  => optional($this->class)->id,
                'name'      => optional($this->class)->name,
            ],
            'election_session' => [
                'election_session_id'   => optional(optional($this->class)->electionSession)->id,
                'name'                  => optional(optional($this->class)->electionSession)->name,
                'start_date'            => optional(optional($this->class)->electionSession)->start_date,
                'end_date'              => optional(optional($this->class)->electionSession)->end_date,
            ],
        ];
    }
}
