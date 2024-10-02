<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'idNumber' => $this->idNumber,
            'contactEmail' => $this->contactEmail,
            'phone' => $this->phone,
            'state' => $this->state,
            'city' => $this->city,
            'address' => $this->address
        ];
    }
}
