<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => $this->categoryObject->category,
            'description' => $this->description,
            'price' => $this->price,
            'tax_id' => $this->tax_id,
            'tax_description' => $this->taxObject->description,
        ];
    }
}
