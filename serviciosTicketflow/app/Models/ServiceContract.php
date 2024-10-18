<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceContract extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'company_id',
        'service_id',
        'term_id'
    ];

}
