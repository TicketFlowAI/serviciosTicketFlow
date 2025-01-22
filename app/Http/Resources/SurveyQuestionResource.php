<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SurveyQuestionResource",
 *     type="object",
 *     title="SurveyQuestionResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="question", type="string", example="¿Cómo calificaría la satisfacción con el servicio de soporte recibido?"),
 *     @OA\Property(property="active", type="boolean", example=true)
 * )
 */
class SurveyQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'status' => $this->status,
        ];
    }
}
