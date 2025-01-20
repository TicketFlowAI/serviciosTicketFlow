<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewClassifierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ClassifierArn' => $this->DocumentClassifierArn,
            'Status' => 'TRAINING',
        ];
    }
}
