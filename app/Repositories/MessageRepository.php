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
   * Retrieves all items.
   */
  public function index(){
     return Message::all();
  }

  /**
   * Retrieves an item by ID.
   */
  public function getById($id){
     return Message::findOrFail($id);
  }

  /**
   * Creates a new item in the repository.
   */
  public function store(array $data){
     return Message::create($data);
  }

  /**
   * Updates an Item by ID.
   */
  public function update(array $data,$id){
     return Message::whereId($id)->update($data);
  }
  
  /**
   * Deletes an Item by ID.
   */
  public function delete($id){
     Message::destroy($id);
  }
}
