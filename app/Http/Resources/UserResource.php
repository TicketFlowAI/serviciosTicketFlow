<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="First name of the user",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="lastname",
 *         type="string",
 *         description="Last name of the user",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Role assigned to the user",
 *         example="admin"
 *     ),
 *     @OA\Property(
 *         property="company_id",
 *         type="integer",
 *         description="ID of the company associated with the user",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="company_name",
 *         type="string",
 *         description="Name of the company associated with the user",
 *         example="TechCorp"
 *     )
 * )
 */
class UserResource extends JsonResource
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
            'lastname' => $this->lastname,
            'email' => $this->email,
            'role' => $this->role,
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'twoFactorEnabled' => $this->twoFactorEnabled ?? 'N/A',
        ];
    }
}
