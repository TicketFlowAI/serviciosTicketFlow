<?php

namespace App\Repositories;
use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;

class MessageRepository implements MessageRepositoryInterface
{
   /**
    * Create a new class instance.
    */
   public function __construct()
   {
      //
   }

   /**
    * Retrieves an item by ID.
    */
   public function getById($id)
   {
      return Message::where('ticket_id', $id)->get();
   }

   /**
    * Creates a new item in the repository.
    */
   public function store(array $data)
   {
      return Message::create($data);
   }
}
