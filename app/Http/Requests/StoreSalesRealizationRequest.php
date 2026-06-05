<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesRealizationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'realizations' => 'required|array|min:1',
            'realizations.*.year' => 'required|integer|min:2000',
            'realizations.*.month' => 'required|integer|min:1|max:12',
            'realizations.*.sales_member_name' => 'required|string|max:255',
            'realizations.*.entity_name' => 'required|string|max:255',
            'realizations.*.realization_amount' => 'required|numeric|min:0',
        ];
    }
}
