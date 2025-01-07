<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ServiceTermResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the service term",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="term",
 *         type="string",
 *         description="Name or description of the service term",
 *         example="Annual"
 *     ),
 *     @OA\Property(
 *         property="months",
 *         type="integer",
 *         description="Number of months for the service term",
 *         example=12
 *     )
 * )
 */
class ServiceTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'term' => $this->term,
            'months' => $this->months,
        ];
    }
}
