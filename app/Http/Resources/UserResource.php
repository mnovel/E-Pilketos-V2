<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if ($this->role != 'voter') {
            return [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'role'  => $this->role,
            ];
        } else {
            return (new ParticipantDetailResource($this->participant))->toArray($request);
        }
    }
}
