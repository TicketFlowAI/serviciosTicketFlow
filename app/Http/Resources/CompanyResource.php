<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CompanyResource",
 *
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the company",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the company",
 *         example="MindSoft Inc."
 *     ),
 *     @OA\Property(
 *         property="idNumber",
 *         type="string",
 *         description="Identification number of the company",
 *         example="123456789"
 *     ),
 *     @OA\Property(
 *         property="contactEmail",
 *         type="string",
 *         description="Contact email of the company",
 *         example="contact@mindsoft.com"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Contact phone number of the company",
 *         example="+1-555-1234"
 *     ),
 *     @OA\Property(
 *         property="state",
 *         type="string",
 *         description="State where the company is located",
 *         example="California"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="City where the company is located",
 *         example="San Francisco"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the company",
 *         example="1234 Market Street"
 *     )
 * )
 */
class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'idNumber' => $this->idNumber,
            'contactEmail' => $this->contactEmail,
            'phone' => $this->phone,
            'state' => $this->state,
            'city' => $this->city,
            'address' => $this->address,
        ];
    }
}
