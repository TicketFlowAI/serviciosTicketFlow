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
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="John"),
 *         @OA\Property(property="lastname", type="string", example="Doe")
 *     ),
 *     @OA\Property(
 *         property="questions",
 *         type="array",
 *         @OA\Items(type="string", example="What is your favorite color?")
 *     ),
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
            'user' => [
                'name' => $this->user->name,
                'lastname' => $this->user->lastname,
            ],
            'questions' => $this->questions->map(function ($question) {
                return $question->question;
            }),
            'score' => $this->score,
        ];
    }
}
