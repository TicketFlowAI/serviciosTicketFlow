<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'service_contract_id',
        'title',
        'priority',
        'needsHumanInteraction',
        'complexity',
        'user_id',
        'status',
        'newClientMessage',
        'newTechnicianMessage',
        'job_id_classifier',
        'job_id_human_intervention',
        'AIresponse',
    ];

    public function service_contract(): BelongsTo
    {
        return $this->belongsTo(ServiceContract::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function message(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function histories()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function survey()
    {
        return $this->hasMany(Survey::class);
    }

}
