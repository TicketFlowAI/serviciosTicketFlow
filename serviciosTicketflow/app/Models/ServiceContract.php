<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceContract extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'company_id',
        'service_id',
        'service_term_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    public function service_term(): HasOne
    {
        return $this->hasOne(ServiceTerm::class);
    }
    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
