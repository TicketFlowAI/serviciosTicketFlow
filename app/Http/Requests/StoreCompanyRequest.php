<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users with appropriate roles to create companies
        return !$this->user()->hasRole('client');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=> 'required|string',
            'idNumber'=> 'required|numeric|unique:companies',
            'contactEmail'=> 'required|email',
            'phone'=> 'required',
            'state'=> 'required|string',
            'city'=> 'required|string',
            'address'=> 'required|string'
        ];
    }

    /**
     * Sends an httpException stating what went wrong with the validation.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
