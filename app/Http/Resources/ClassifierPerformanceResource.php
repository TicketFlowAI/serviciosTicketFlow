<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassifierPerformanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'VersionArn' => $this->VersionArn,
            'Accuracy' => $this->Accuracy,
            'F1Score' => $this->F1Score,
            'Precision' => $this->Precision,
            'Recall' => $this->Recall,
        ];
    }
}
