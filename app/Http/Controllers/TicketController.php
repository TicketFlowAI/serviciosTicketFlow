<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Company;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    private TicketRepositoryInterface $ticketRepositoryInterface;

    public function __construct(TicketRepositoryInterface $ticketRepositoryInterface)
    {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * Verify that the client user can access the resource.
     */
    private function verifyCompanyAccess($resourceCompanyId)
    {
        $user = Auth::user();

        // If user has role 'client' and the company's ID doesn't match, deny access
        if ($user->hasRole('client') && $user->company_id !== $resourceCompanyId) {
            abort(403, 'Unauthorized access to resource.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();


        // Filter data based on company for client users
        if ($user->hasRole('client')) {
            $data = $this->ticketRepositoryInterface->getTicketsByCompany($user->company_id);
        } else {
            $data = $this->ticketRepositoryInterface->index();
        }

        $data->load('user:id,name,lastname', 'service_contract:id,company_id,service_id');
        foreach ($data as $ticket) {
            $ticket->company = Company::where('id', $ticket->service_contract->company_id)->first();
            $ticket->service = Service::where('id', $ticket->service_contract->service_id)->first();
        }

        return ApiResponseClass::sendResponse(TicketResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $serviceContract = $this->ticketRepositoryInterface->getById($request->service_contract_id);

        // Verify that the user has access to this service contract
        $this->verifyCompanyAccess($serviceContract->company_id);

        $details = [
            'service_contract_id' => $request->service_contract_id,
            'title' => $request->title,
            'priority' => $request->priority,
            'needsHumanInteraction' => $request->needsHumanInteraction,
            'complexity' => $request->complexity,
            'user_id' => $request->user_id
        ];

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

        // Verify that the user has access to this ticket
        $this->verifyCompanyAccess($data->service_contract->company_id);

        $data->load('user:id,name,lastname', 'service_contract:id,company_id,service_id');

        $data->company = Company::where('id', $data->service_contract->company_id)->first();
        $data->service = Service::where('id', $data->service_contract->service_id)->first();

        return ApiResponseClass::sendResponse(new TicketResource($data), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = $this->ticketRepositoryInterface->getById($id);

        // Verify that the user has access to this ticket
        $this->verifyCompanyAccess($ticket->service_contract->company_id);

        $updateDetails = [
            'service_contract_id' => $request->service_contract_id,
            'title' => $request->title,
            'priority' => $request->priority,
            'needsHumanInteraction' => $request->needsHumanInteraction,
            'complexity' => $request->complexity,
            'user_id' => $request->user_id,
            'status' => $request->status
        ];

        DB::beginTransaction();
        try {
            $ticket = $this->ticketRepositoryInterface->update($updateDetails, $id);

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
        $ticket = $this->ticketRepositoryInterface->getById($id);

        // Verify that the user has access to this ticket
        $this->verifyCompanyAccess($ticket->service_contract->company_id);

        $this->ticketRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Ticket Delete Successful', '', 204);
    }
}
