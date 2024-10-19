<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'service_contract_id' => $this->service_contract_id,
            'title' => $this->title,
            'priority' => $this->priority,
            'needsHumanInteraction' => $this->nedsHumanInteraction,
            'complexity' => $this->complexity,
            'user_id' => $this->user_id
        ];
    }
}
