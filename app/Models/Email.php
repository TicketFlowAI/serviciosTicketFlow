<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'subject',
        'body'
    ];

    public function interval(): HasOne
    {
        return $this->hasOne(Interval::class);
    }
}
