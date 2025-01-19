<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketHistoryResource;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Company;
use App\Models\Service;
use App\Models\TicketHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    private const USER_FIELDS = 'user:id,name,lastname';
    private const SERVICE_CONTRACT_FIELDS = 'service_contract:id,company_id,service_id';

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
        try {
            $user = Auth::user();

            if ($user->hasRole('client')) {
                $data = $this->ticketRepositoryInterface->getTicketsByCompany($user->company_id);
            } elseif ($user->hasRole('technician')) {
                $data = $this->ticketRepositoryInterface->getTicketsByTechnician($user->id);
            } else {
                $data = $this->ticketRepositoryInterface->index();
            }

            $data->load(self::SERVICE_CONTRACT_FIELDS, self::USER_FIELDS);
            foreach ($data as $ticket) {
                $ticket->company = Company::find($ticket->service_contract->company_id);
                $ticket->service = Service::find($ticket->service_contract->service_id);
                if (is_null($ticket->user)) {
                    $ticket->user = (object) ['name' => 'Asignando', 'lastname' => ''];
                }
                if (is_null($ticket->priority) || is_null($ticket->complexity) || is_null($ticket->needsHumanInteraction)) {
                    $ticket->priority = 'Asignando';
                    $ticket->complexity = 'Asignando';
                    $ticket->needsHumanInteraction = 'Asignando';
                }
            }

            return ApiResponseClass::sendResponse(TicketResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve tickets', 500);
        }
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

            // Log ticket creation in history
            $this->recordHistory($ticket->id, 'creado');

            DB::commit();
            return ApiResponseClass::sendResponse(new TicketResource($ticket), 'Ticket Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create ticket', 500);
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
        try {
            $data = $this->ticketRepositoryInterface->getById($id);

            $data->load(self::SERVICE_CONTRACT_FIELDS, self::USER_FIELDS);
            $data->company = Company::find($data->service_contract->company_id);
            $data->service = Service::find($data->service_contract->service_id);
            if (is_null($data->user)) {
                $data->user = (object) ['name' => 'Asignando', 'lastname' => ''];
            }
            if (is_null($data->priority) || is_null($data->complexity) || is_null($data->needsHumanInteraction)) {
                $data->priority = 'Asignando';
                $data->complexity = 'Asignando';
                $data->needsHumanInteraction = 'Asignando';
            }

            return ApiResponseClass::sendResponse(new TicketResource($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve ticket', 500);
        }
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

            // Log ticket update in history
            $this->recordHistory($id, 'actualizado');

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Update Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update ticket', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tickets/close/{id}",
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
        DB::beginTransaction();
        try {
            $ticket = $this->ticketRepositoryInterface->getById($id);

            if ($ticket->survey) {
                $details = ['status' => 0];
                $historyMessage = 'Encuesta completada';
            } else {
                $details = ['status' => 3];
                $historyMessage = 'Ticket cerrado';
            }

            $this->ticketRepositoryInterface->update($details, $id);

            // Log ticket closure in history
            $this->recordHistory($id, $historyMessage);

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Close Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to close ticket', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tickets/open/{id}",
     *     summary="Open a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket opened successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function openTicket($id)
    {
        $details = [
            'status' => 1
        ];

        DB::beginTransaction();
        try {
            $this->ticketRepositoryInterface->update($details, $id);

            // Log ticket opening in history
            $this->recordHistory($id, 'abierto');

            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Open Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to open ticket', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tickets/reassign/{id}",
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
            $ticket = $this->ticketRepositoryInterface->getById($id);

            if (!$ticket) {
                $response = ApiResponseClass::sendResponse('Ticket not found', '', 404);
            } else {
                $technicians = $this->getAvailableTechnicians($ticket);

                if ($technicians->isEmpty()) {
                    $response = ApiResponseClass::sendResponse('No technicians available', '', 400);
                } else {
                    if ($ticket->user_id) {
                        $ticket->complexity += 1;
                        $technicians = $this->getAvailableTechnicians($ticket, $ticket->user_id);

                        if ($technicians->isEmpty()) {
                            $response = ApiResponseClass::sendResponse('No available technicians other than the current assignee', '', 400);
                        } else {
                            $this->recordHistory($id, 'Solicitado reasignación por ' . Auth::user()->name . ' ' . Auth::user()->lastname);
                        }
                    }

                    if (!isset($response)) {
                        $technicianWithLeastTickets = $this->findTechnicianWithLeastTickets($technicians);
                        $this->assignTicketToTechnician($ticket, $technicianWithLeastTickets);
                        $response = ApiResponseClass::sendResponse('Ticket assigned/reassigned successfully', '', 200);
                    }
                }
            }

            DB::commit();
            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to assign/reassign ticket', 500);
        }
    }

    private function getAvailableTechnicians($ticket, $excludeUserId = null)
    {
        return Role::where('name', 'technician')->first()->users->filter(function ($technician) use ($ticket, $excludeUserId) {
            return $technician->roles->pluck('name')->intersect(['1', '2', '3'])->isNotEmpty() &&
                   $technician->roles->pluck('name')->contains($ticket->complexity) &&
                   (!$excludeUserId || $technician->id !== $excludeUserId);
        });
    }

    private function findTechnicianWithLeastTickets($technicians)
    {
        return $technicians->sortBy(function ($technician) {
            return $technician->ticket()->count();
        })->first();
    }

    private function assignTicketToTechnician($ticket, $technician)
    {
        $ticket->user_id = $technician->id;
        $ticket->save();
        $this->recordHistory($ticket->id, 'Asignado a ' . $technician->name . ' ' . $technician->lastname);
    }

    /**
     * @OA\Get(
     *     path="/tickets/{id}/history",
     *     summary="Retrieve the history of a specific ticket",
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
     *         description="Ticket history retrieved successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/TicketHistoryResource"))
     *     ),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function retrieveTicketHistory($id)
    {
        try {
            $history = TicketHistory::where('ticket_id', $id)->latest()->get();
            $history->load('user:id,name,lastname');
            return ApiResponseClass::sendResponse(TicketHistoryResource::collection($history), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve ticket history', 500);
        }
    }

    public function recordHistory($ticketId, $action)
    {
        TicketHistory::create([
            'ticket_id' => $ticketId,
            'user_id' => Auth::id(),
            'action' => $action,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tickets/deleted",
     *     summary="Get a list of deleted tickets",
     *     tags={"Tickets"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TicketResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->ticketRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(TicketResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted tickets', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/tickets/{id}/restore",
     *     summary="Restore a deleted ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket restored successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->ticketRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('Ticket Restore Successful', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore ticket', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tickets/{id}/needs-human-interaction",
     *     summary="Mark ticket as needing human interaction and assign it",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ticket marked and assigned successfully"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function markNeedsHumanInteractionAndAssign($id)
    {
        DB::beginTransaction();
        try {
            $ticket = $this->ticketRepositoryInterface->getById($id);

            if (!$ticket) {
                DB::rollBack();
                return ApiResponseClass::sendResponse('Ticket not found', '', 404);
            }

            $ticket->needsHumanInteraction = 1;
            $ticket->save();

            $response = $this->assignTicket($id);

            DB::commit();
            return $response;
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to mark ticket as needing human interaction and assign', 500);
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
     *     @OA\Response(response=404, description="Ticket not found"),
     *     @OA\Response(response=400, description="Cannot delete, histories associated")
     * )
     */
    public function destroy($id)
    {
        try {
            // Check for associated histories
            $ticket = $this->ticketRepositoryInterface->getById($id);
            if ($ticket->histories()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete, histories associated', 400);
            }

            $this->ticketRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Ticket Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete ticket', 500);
        }
    }
}
