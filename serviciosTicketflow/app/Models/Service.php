<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'description',
        'price',
        'tax_id',
    ];

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function tax(): HasOne
    {
        return $this->hasOne(Tax::class);
    }
    public function serviceContract(): HasMany
    {
        return $this->hasMany(ServiceContract::class);
    }
}
