<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateCompanyRequest extends FormRequest
{
    private const REQUIRED_STRING = 'required|string';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $companyId = $this->route('company');

        // Allow access if the user is not a client
        if (!$user->hasRole('client')) {
            return true;
        }

        // For client users, ensure they own the company
        return $user->company_id === (int) $companyId;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('company'); // Company ID from the route

        // Restrict rules for client users
        if ($this->user()->hasRole('client')) {
            return [
                'contactEmail' => 'required|email',
                'phone' => self::REQUIRED_STRING,
                'state' => self::REQUIRED_STRING,
                'city' => self::REQUIRED_STRING,
                'address' => self::REQUIRED_STRING,
            ];
        }

        // Default rules for other roles
        return [
            'name' => 'required|string|unique:companies,name,' . $id,
            'idNumber' => 'required|numeric|unique:companies,idNumber,' . $id,
            'contactEmail' => 'required|email',
            'phone' => self::REQUIRED_STRING,
            'state' => self::REQUIRED_STRING,
            'city' => self::REQUIRED_STRING,
            'address' => self::REQUIRED_STRING,
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

