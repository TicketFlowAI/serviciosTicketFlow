<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceContractResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company' => $this->company->name,
            'service_id' => $this->service_id,
            'service' => $this->service->description,
            'service_term_id' => $this->service_term_id,
            'service_term' => $this->service_term->term,
            'price' => $this->price
        ];
    }
}
