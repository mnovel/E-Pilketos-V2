<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassDetailResource extends JsonResource
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
                'start_date'            => optional($this->electionSession)->start_date,
                'end_date'              => optional($this->electionSession)->end_date,
            ],
            'participants_info' => [
                'pending'   => $this->participants->where('user.status', 'pending')->count(),
                'active'  => $this->participants->where('user.status', 'active')->count(),
                'inactive'  => $this->participants->where('user.status', 'inactive')->count(),
                'total'     => $this->participants->count(),
            ],
            'participants_votes_info' => [
                'not_started'   => $this->participants->where('user.status', 'active')->where('voting_status', null)->count(),
                'waiting'       => $this->participants->where('user.status', 'active')->where('voting_status', 'waiting')->count(),
                'in_progress'   => $this->participants->where('user.status', 'active')->where('voting_status', 'in_progress')->count(),
                'completed'     => $this->participants->where('user.status', 'active')->where('voting_status', 'completed')->count(),
            ],
        ];
    }
}
