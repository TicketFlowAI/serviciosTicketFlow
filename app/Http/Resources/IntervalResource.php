<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="IntervalResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the interval",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="days",
 *         type="integer",
 *         description="Number of days for the interval",
 *         example=7
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Type of interval (e.g., weekly, monthly)",
 *         example="Weekly"
 *     ),
 *     @OA\Property(
 *         property="template_name",
 *         type="string",
 *         description="Name of the associated template",
 *         example="Weekly Report Template"
 *     ),
 *     @OA\Property(
 *         property="subject_template",
 *         type="string",
 *         description="Subject template for the interval",
 *         example="Weekly Summary"
 *     )
 * )
 */
class IntervalResource extends JsonResource
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
            'days' => $this->days,
            'type' => $this->type,
            'email_id' => $this->email_id,
            'template_name' => $this->email->template_name,
        ];
    }
}
