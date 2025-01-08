<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TicketResource",
 *     type="object",
 *     description="Ticket resource schema",
 *     @OA\Property(property="id", type="integer", description="Unique identifier of the ticket", example=1),
 *     @OA\Property(property="service_contract_id", type="integer", description="ID of the associated service contract", example=10),
 *     @OA\Property(property="service_id", type="integer", description="ID of the service", example=5),
 *     @OA\Property(property="service_desc", type="string", description="Description of the service", example="Technical Support"),
 *     @OA\Property(property="company_id", type="integer", description="ID of the associated company", example=3),
 *     @OA\Property(property="company_name", type="string", description="Name of the company", example="TechCorp"),
 *     @OA\Property(property="title", type="string", description="Title of the ticket", example="System Outage"),
 *     @OA\Property(property="priority", type="string", description="Priority of the ticket", example="High"),
 *     @OA\Property(property="needsHumanInteraction", type="boolean", description="Whether the ticket requires human interaction", example=true),
 *     @OA\Property(property="complexity", type="string", description="Complexity level of the ticket", example="Medium"),
 *     @OA\Property(property="user_id", type="integer", description="ID of the user who created the ticket", example=12),
 *     @OA\Property(property="user_name", type="string", description="Name of the user who created the ticket", example="John"),
 *     @OA\Property(property="user_lastname", type="string", description="Lastname of the user who created the ticket", example="Doe"),
 *     @OA\Property(property="status", type="string", description="Current status of the ticket", example="Open"),
 *     @OA\Property(property="newClientMessage", type="boolean", description="Indicates if there's a new message from the client", example=false),
 *     @OA\Property(property="newTechnicianMessage", type="boolean", description="Indicates if there's a new message from the technician", example=true)
 * )
 */
class TicketResource extends JsonResource
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
            'service_contract_id' => $this->service_contract_id,
            'service_id' => $this->service->id,
            'service_desc' => $this->service->description,
            'company_id'=> $this->service_contract->company_id,
            'company_name'=> $this->company->name,
            'title' => $this->title,
            'priority' => $this->priority,
            'needsHumanInteraction' => $this->needsHumanInteraction,
            'complexity' => $this->complexity,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'user_lastname' => $this->user->lastname,
            'status' => $this->status,
            'newClientMessage' => $this->newClientMessage,
            'newTechnicianMessage' => $this->newTechnicianMessage,
        ];
    }
}
