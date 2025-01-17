<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Survey",
 *     type="object",
 *     title="Survey",
 *     @OA\Property(property="ticket_id", type="integer", example=1),
 *     @OA\Property(property="question_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="score", type="integer", example=5),
 * )
 */
class SurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ticket_id' => $this->ticket_id,
            'question_id' => $this->question_id,
            'user_id' => $this->user_id,
            'score' => $this->score,
        ];
    }
}
