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
            'service_desc' => $this->serviceObject->description,
            'service_id' => $this->serviceObject->id,
            'company_id'=> $this->companyObject->id,
            'company_name'=> $this->companyObject->name,
            'title' => $this->title,
            'priority' => $this->priority,
            'needsHumanInteraction' => $this->nedsHumanInteraction,
            'complexity' => $this->complexity,
            'user_id' => $this->user_id,
            'user_name' => $this->userObject->name,
            'user_lastname' => $this->userObject->lastname
        ];
    }
}
