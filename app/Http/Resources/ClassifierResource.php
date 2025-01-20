<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassifierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ClassifierName' => $this->ClassifierName,
            'ClassifierArn' => $this->ClassifierArn,
            'VersionName' => $this->VersionName,
            'Status' => $this->Status,
            'LanguageCode' => $this->LanguageCode,
            'SubmitTime' => $this->SubmitTime,
            'EndTime' => $this->EndTime,
            'NumberOfLabels' => $this->NumberOfLabels,
            'Accuracy' => $this->Accuracy,
            'F1Score' => $this->F1Score,
            'Precision' => $this->Precision,
            'Recall' => $this->Recall,
        ];
    }
}
