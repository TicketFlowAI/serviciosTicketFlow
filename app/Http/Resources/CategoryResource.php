<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class CategoryResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="CategoryResource",
     *     type="object",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="Unique identifier for the category",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="category",
     *         type="string",
     *         description="Name of the category",
     *         example="Electronics"
     *     )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category
        ];
    }
}
