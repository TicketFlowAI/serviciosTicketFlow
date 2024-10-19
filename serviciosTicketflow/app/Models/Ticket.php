<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'user_id'
    ];

    public function service_contract(): BelongsTo
    {
        return $this->belongsTo(ServiceContract::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
