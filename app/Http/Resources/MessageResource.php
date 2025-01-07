<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MessageResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the message",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="ticket_id",
 *         type="integer",
 *         description="ID of the associated ticket",
 *         example=123
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Content of the message",
 *         example="This is a message content."
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who created the message",
 *         example=45
 *     ),
 *     @OA\Property(
 *         property="user_name",
 *         type="string",
 *         description="First name of the user who created the message",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="user_lastname",
 *         type="string",
 *         description="Last name of the user who created the message",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="user_role",
 *         type="string",
 *         description="Role of the user who created the message",
 *         example="technician"
 *     ),
 *     @OA\Property(
 *         property="timestamp",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp of when the message was created",
 *         example="2024-01-01T12:34:56Z"
 *     )
 * )
 */
class MessageResource extends JsonResource
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
            'ticket_id' => $this->ticket_id,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'user_lastname' => $this->user->lastname,
            'user_role' => $this->userRole,
            'timestamp' => $this->created_at,
        ];
    }
}
