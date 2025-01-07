<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

/**
 * @OA\Tag(
 *     name="Tickets",
 *     description="API Endpoints for managing tickets"
 * )
 */
class TicketController extends Controller
{
    private TicketRepositoryInterface $ticketRepositoryInterface;

    public function __construct(TicketRepositoryInterface $ticketRepositoryInterface)
    {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/tickets",
     *     summary="Get a list of tickets",
     *     tags={"Tickets"},
     *     @OA\Response(
     *         response=200,
     *         description="List of tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TicketResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('client')) {
            // Filter data based on company for client users
            $data = $this->ticketRepositoryInterface->getTicketsByCompany($user->company_id);
        } elseif ($user->hasRole('technician')) {
            // Return tickets assigned to the technician
            $data = $this->ticketRepositoryInterface->getTicketsByTechnician($user->id);
        } else {
            // Return all tickets for other roles
            $data = $this->ticketRepositoryInterface->index();
        }

        $data->load('user:id,name,lastname', 'service_contract:id,company_id,service_id');
        foreach ($data as $ticket) {
            $ticket->company = Company::find($ticket->service_contract->company_id);
            $ticket->service = Service::find($ticket->service_contract->service_id);
        }

        return ApiResponseClass::sendResponse(TicketResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/tickets",
     *     summary="Create a new ticket",
     *     tags={"Tickets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "company_id", "service_id"},
     *             @OA\Property(property="title", type="string", example="Ticket Title"),
     *             @OA\Property(property="description", type="string", example="Detailed description of the issue"),
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TicketResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreTicketRequest $request)
    {
        $details = $request->validated();

        DB::beginTransaction();
        try {
            $ticket = $this->ticketRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new TicketResource($ticket), 'Ticket Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/tickets/{id}",
     *     summary="Get details of a specific ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TicketResource")
     *     ),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function show($id)
    {
        $data = $this->ticketRepositoryInterface->getById($id);

        $data->load('user:id,name,lastname', 'service_contract:id,company_id,service_id');
        $data->company = Company::find($data->service_contract->company_id);
        $data->service = Service::find($data->service_contract->service_id);

        return ApiResponseClass::sendResponse(new TicketResource($data), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/tickets/{id}",
     *     summary="Update a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string", example="Updated Ticket Title"),
     *             @OA\Property(property="description", type="string", example="Updated description of the issue")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ticket updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateTicketRequest $request, $id)
    {
        $updateDetails = $request->validated();

        DB::beginTransaction();
        try {
            $this->ticketRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tickets/{id}",
     *     summary="Delete a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Ticket deleted successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function destroy($id)
    {
        $this->ticketRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Ticket Delete Successful', '', 204);
    }

    /**
     * @OA\Patch(
     *     path="/tickets/{id}/close",
     *     summary="Close a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket closed successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function closeTicket($id)
    {
        $details = [
            'status' => 0
        ];

        DB::beginTransaction();
        try {
            $this->ticketRepositoryInterface->update($details, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Close Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Patch(
     *     path="/tickets/{id}/assign",
     *     summary="Assign or reassign a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket assigned successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function assignTicket($id)
    {
        DB::beginTransaction();
        try {
            // Get the ticket by ID
            $ticket = $this->ticketRepositoryInterface->getById($id);

            if (!$ticket) {
                return ApiResponseClass::sendResponse('Ticket not found', '', 404);
            }

            // Get all users with the role 'technician'
            $technicians = Role::where('name', 'technician')->first()->users;

            if ($technicians->isEmpty()) {
                return ApiResponseClass::sendResponse('No technicians available', '', 400);
            }

            if (!$ticket->user_id) {
                // Filter technicians based on ticket complexity
                $technicians = $technicians->filter(function ($technician) use ($ticket) {
                    return $technician->hasRole((string) $ticket->complexity);
                });

                if ($technicians->isEmpty()) {
                    return ApiResponseClass::sendResponse('No technicians available for this complexity level', '', 400);
                }
            } else {
                // Filter technicians with roles higher than the current technician
                $currentTechnician = $technicians->where('id', $ticket->user_id)->first();

                $technicians = $technicians->filter(function ($technician) use ($currentTechnician) {
                    $currentRoles = $currentTechnician->getRoleNames();
                    return $technician->hasAnyRole(array_map(fn($role) => (string) ((int) $role + 1), $currentRoles->toArray()));
                });

                if ($technicians->isEmpty()) {
                    return ApiResponseClass::sendResponse('No technicians available of a higher level', '', 400);
                }
            }

            // Filter by the technician with the least number of tickets with status not equal to 0
            $technicianWithLeastTickets = $technicians->sortBy(function ($technician) {
                return $technician->tickets()->where('status', '!=', 0)->count();
            })->first();

            // Assign the ticket to the technician
            $ticket->user_id = $technicianWithLeastTickets->id;
            $ticket->save();

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket assigned successfully', '', 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return ApiResponseClass::rollback($ex);
        }
    }
}
