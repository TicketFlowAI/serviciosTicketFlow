<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateTicketRequest extends FormRequest
{
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
        $this->merge([
            'needsHumanInteraction' => $this->input('needsHumanInteraction') === '' ? null : $this->input('needsHumanInteraction'),
            'priority' => is_numeric($this->input('priority')) ? (int) $this->input('priority') : null,
            'complexity' => is_numeric($this->input('complexity')) ? (int) $this->input('complexity') : null,
        ]);
        return [
            'service_contract_id' => 'required|numeric',
            'title' => 'required|string',
            'needsHumanInteraction' => 'nullable|boolean',
            'status' => 'nullable|integer',
            'priority' => 'nullable|integer|between:1,5',
            'complexity' => 'nullable|integer|between:1,3',
        ];
    }

    /**
     * Sends an httpException stating what went wrong with the validation.
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
