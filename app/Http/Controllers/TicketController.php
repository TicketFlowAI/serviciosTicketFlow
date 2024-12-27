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

class TicketController extends Controller
{
    private TicketRepositoryInterface $ticketRepositoryInterface;

    public function __construct(TicketRepositoryInterface $ticketRepositoryInterface)
    {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Filter data based on company for client users
        $data = $user->hasRole('client') 
            ? $this->ticketRepositoryInterface->getTicketsByCompany($user->company_id)
            : $this->ticketRepositoryInterface->index();

        $data->load('user:id,name,lastname', 'service_contract:id,company_id,service_id');
        foreach ($data as $ticket) {
            $ticket->company = Company::find($ticket->service_contract->company_id);
            $ticket->service = Service::find($ticket->service_contract->service_id);
        }

        return ApiResponseClass::sendResponse(TicketResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->ticketRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Ticket Delete Successful', '', 204);
    }

    /**
     * Marks a ticket as closed by ID.
     */
    public function closeTicket($id)
    {
        $details = [
            'status' =>  0
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

    public function reassignTicket($id)
    {
        DB::beginTransaction();
        try {
            // Obtener el ticket por ID
            $ticket = $this->ticketRepositoryInterface->getById($id);
    
            if (!$ticket) {
                return ApiResponseClass::sendResponse('Ticket not found', '', 404);
            }
    
            // Obtener todos los usuarios con el rol 'technician'
            $technicians = Role::where('name', 'technician')->first()->users;
    
            if ($technicians->isEmpty()) {
                return ApiResponseClass::sendResponse('No technicians available', '', 400);
            }
    
            // Filtrar para excluir al técnico actualmente asignado
            $technicians = $technicians->filter(function ($technician) use ($ticket) {
                return $technician->id !== $ticket->user_id;
            });
    
            if ($technicians->isEmpty()) {
                return ApiResponseClass::sendResponse('No available technicians other than the current assignee', '', 400);
            }
    
            // Encontrar al técnico con menos tickets asignados
            $technicianWithLeastTickets = $technicians->sortBy(function ($technician) {
                return $technician->tickets()->count();
            })->first();
    
            // Reasignar el ticket al técnico
            $ticket->user_id = $technicianWithLeastTickets->id;
            $ticket->save();
    
            DB::commit();
            return ApiResponseClass::sendResponse('Ticket reassigned successfully', '', 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return ApiResponseClass::rollback($ex);
        }
    }
}
