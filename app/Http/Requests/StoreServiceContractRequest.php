<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreServiceContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Allow non-client roles to create service contracts
        if (!$user->hasRole('client')) {
            return true;
        }

        // For client users, ensure the company_id matches their company
        $companyId = $this->input('company_id', $user->company_id);

        return $companyId == $user->company_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|numeric|exists:companies,id',
            'service_id' => 'required|numeric|exists:services,id',
            'service_term_id' => 'required|numeric|exists:service_terms,id',
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
