<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateServiceTermRequest extends FormRequest
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
        // Get the ID of the resource being updated from the route
        $id = $this->route('serviceTerm'); // Ensure 'serviceTerm' matches the route parameter name
    
        return [
            'term' => [
                'required',
                'string',
                Rule::unique('service_terms', 'term')->ignore($id),
            ],
            'months' => [
                'required',
                'numeric',
                Rule::unique('service_terms', 'months')->ignore($id),
            ],
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
        ]));
    }
}
