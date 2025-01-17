<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\SurveyQuestionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['question'];
}
