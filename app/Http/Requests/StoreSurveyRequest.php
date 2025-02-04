<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreSurveyRequest extends FormRequest
{
    private const INTEGER_RULES = 'required|integer';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'SurveyAnswers' => 'required|array',
            'SurveyAnswers.*.ticket_id' => self::INTEGER_RULES,
            'SurveyAnswers.*.question_id' => self::INTEGER_RULES,
            'SurveyAnswers.*.user_id' => self::INTEGER_RULES,
            'SurveyAnswers.*.score' => 'required|integer|min:0|max:5',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ], 500));
    }
}
