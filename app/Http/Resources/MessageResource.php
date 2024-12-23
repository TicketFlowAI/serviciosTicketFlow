<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' =>$this->id,
            'ticket_id' => $this->ticket_id,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'user_name'=>$this->user->name,
            'user_lastname'=>$this->user->lastname,
            'user_role'=>$this->userRole,
            'timestamp'=>$this->created_at,
        ];
    }
}
