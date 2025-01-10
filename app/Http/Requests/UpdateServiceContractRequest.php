<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateServiceContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $serviceContract = $this->route('service_contract'); // Use route binding

        // Allow non-client roles to proceed
        if (!$user->hasRole('client')) {
            return true;
        }

        // For client users, ensure the service contract belongs to their company
        return $serviceContract && $serviceContract->company_id === $user->company_id;
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
