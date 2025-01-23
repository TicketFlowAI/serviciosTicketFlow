<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Log;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'ticket_id',
        'content',
        'user_id'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sets automatically a flag in the ticket
     * that states that a new message has been registered depending
     * on the role of the use who wrote it
     * @return void
     */
    // protected static function booted()
    // {
    //     static::created(function ($message) {
    //         $user = Auth::user(); // Get the currently authenticated user
    //         Log ::info('User: ' . $user->name . ' created a message with content: ' . $message->content);
    //         if ($user->hasRole('technician' ) || $user->hasRole('super-admin')) {
    //             // Set NewTechnicianMessage if the message is from a technician
    //             $message->ticket->update(['newTechnicianMessage' => true]);
    //         } elseif ($user->hasRole('client')) {
    //             // Set NewClientMessage if the message is from a client
    //             $message->ticket->update(['newClientMessage' => true]);
    //         }
    //     });
    // }
}
