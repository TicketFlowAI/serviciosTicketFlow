<?php

namespace App\Repositories;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\Ticket;
use App\Models\User;

class TicketRepository implements TicketRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieves all items.
     */
    public function index()
    {
        $tickets = Ticket::all();
        foreach ($tickets as $ticket) {
         $ticket->serviceContractObject = ServiceContract::where('id', $ticket->service_contract_id)->first();
         $ticket->serviceObject = Service::where('id', $ticket->serviceContractObject->service_id)->first();
         $ticket->companyObject = Company::where('id', $ticket->serviceContractObject->company_id)->first();
         $ticket->userObject = User::where('id', $ticket->user_id)->first();
        }
        return $tickets;
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return Ticket::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return Ticket::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return Ticket::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        Ticket::destroy($id);
    }
}
