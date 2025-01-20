<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'survey_question_id',
        'user_id',
        'score',
    ];

    public function survey_questions()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
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
