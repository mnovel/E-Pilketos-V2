<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarcodeCheckinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'session_name' => $this['session'] ? $this['session']->name : null,
            'start_date'   => $this['session'] ? $this['session']->start_date : null,
            'end_date'     => $this['session'] ? $this['session']->end_date : null,
            'class_names'  => $this['classes'] ? $this['classes']->pluck('name')->toArray() : [],
            'barcode'      => $this['barcode'] ?? null,
        ];
    }
}
