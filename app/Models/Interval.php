<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Interval extends Model
{
    use HasFactory;

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
