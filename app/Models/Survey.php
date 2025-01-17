<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{

    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'question_id',
        'user_id',
        'score',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
