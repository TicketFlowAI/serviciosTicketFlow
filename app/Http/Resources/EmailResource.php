<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="EmailResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the email template",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="template_name",
 *         type="string",
 *         description="Name of the email template",
 *         example="Welcome Email"
 *     ),
 *     @OA\Property(
 *         property="body",
 *         type="string",
 *         description="Body of the email template",
 *         example="Welcome to our service!"
 *     )
 * )
 */
class EmailResource extends JsonResource
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
            'template_name' => $this->template_name,
            'body' => $this->body,
        ];
    }
}
