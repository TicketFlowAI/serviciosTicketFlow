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
            'service_id' => $this->service->id,
            'service_desc' => $this->service->description,
            'company_id'=> $this->service_contract->company_id,
            'company_name'=> $this->company->name,
            'title' => $this->title,
            'priority' => $this->priority,
            'needsHumanInteraction' => $this->needsHumanInteraction,
            'complexity' => $this->complexity,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'user_lastname' => $this->user->lastname,
            'status' =>$this->status,
            'newClientMessage' =>$this->newClientMessage,
            'newTechnicianMessage' =>$this->newTechnicianMessage,
        ];
    }
}
