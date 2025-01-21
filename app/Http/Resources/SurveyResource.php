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
 *     @OA\Property(property="user_name", type="string", example="John"),
 *     @OA\Property(property="user_lastname", type="string", example="Doe"),
 *     @OA\Property(property="question", type="string", example="What is your favorite color?"),
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
            'user_name' => $this->user->name,
            'user_lastname' => $this->user->lastname,
            'question_id' => $this->questions->id,
            'question' => $this->questions->question,
            'score' => $this->score,
        ];
    }
}
