<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTerm extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'term',
        'months'
    ];

    public function serviceContract(): HasMany
    {
        return $this->hasMany(ServiceContract::class);
    }

    protected $table = 'service_terms';
}
