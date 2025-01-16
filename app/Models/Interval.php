<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'days',
        'type',
        'email_id',
    ];



    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}
