<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ServiceResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the service",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         description="ID of the associated category",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Name of the category",
 *         example="Software"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the service",
 *         example="Premium Support Service"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Price of the service",
 *         example=99.99
 *     ),
 *     @OA\Property(
 *         property="tax_id",
 *         type="integer",
 *         description="ID of the associated tax",
 *         example=3
 *     ),
 *     @OA\Property(
 *         property="tax_description",
 *         type="string",
 *         description="Description of the tax",
 *         example="VAT 15%"
 *     ),
 *     @OA\Property(
 *         property="details",
 *         type="string",
 *         description="Details of the service",
 *         example="This service includes 24/7 support."
 *     )
 * )
 */
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
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => $this->category->category,
            'description' => $this->description,
            'price' => $this->price,
            'tax_id' => $this->tax_id,
            'tax_description' => $this->tax->description,
            'details' => $this->details,
        ];
    }
}
