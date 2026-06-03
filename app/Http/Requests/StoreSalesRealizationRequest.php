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
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|min:1|max:12',
            'sales_member_id' => 'required|exists:sales_members,id',
            'entity_id' => 'required|exists:entities,id',
            'realization_amount' => 'required|numeric|min:0',
        ];
    }
}
