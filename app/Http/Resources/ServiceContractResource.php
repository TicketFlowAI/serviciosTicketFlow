<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ServiceContractResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the service contract",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="company_id",
 *         type="integer",
 *         description="ID of the associated company",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="company",
 *         type="string",
 *         description="Name of the associated company",
 *         example="MindSoft Inc."
 *     ),
 *     @OA\Property(
 *         property="service_id",
 *         type="integer",
 *         description="ID of the associated service",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="service",
 *         type="string",
 *         description="Description of the associated service",
 *         example="Premium Support"
 *     ),
 *     @OA\Property(
 *         property="service_term_id",
 *         type="integer",
 *         description="ID of the associated service term",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="service_term",
 *         type="string",
 *         description="Term duration for the service",
 *         example="12 months"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Calculated monthly price for the service",
 *         example=100.50
 *     ),
 *     @OA\Property(
 *         property="expiration_date",
 *         type="string",
 *         format="date",
 *         description="Expiration date of the service contract in DD/MM/YYYY format",
 *         example="01/07/2023"
 *     )
 * )
 */
class ServiceContractResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company' => $this->company->name,
            'service_id' => $this->service_id,
            'service' => $this->service->description,
            'service_term_id' => $this->service_term_id,
            'service_term' => $this->serviceterm->term,
            'price' => $this->price,
            'expiration_date' => $this->expiration_date,
        ];
    }
}
