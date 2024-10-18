<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'idNumber',
        'contactEmail',
        'phone',
        'state',
        'city',
        'address'
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
