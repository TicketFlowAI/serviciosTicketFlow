<?php

namespace App\Repositories;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

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
        return Ticket::all();
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

    /**
     * Retrieves tickets by company
     */
    public function getTicketsByCompany($id)
    {
        
        return Ticket::whereHas('service_contract', function ($query) use ($id) {
            $query->where('company_id', $id); // Use '=' for exact match
        })->with('service_contract')->get();
    }
}
