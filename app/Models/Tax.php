<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'description',
        'value'
    ];

    public function service(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}