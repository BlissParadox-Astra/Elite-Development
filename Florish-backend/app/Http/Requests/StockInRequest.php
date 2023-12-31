<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'stock_in_requests' => 'required|array',
            'stock_in_requests.*.reference_number' => 'required|string',
            'stock_in_requests.*.product_id' => 'required|exists:products,id',
            'stock_in_requests.*.quantity_added' => 'required|integer|min:1',
            'stock_in_requests.*.stock_in_date' => 'required|date',
        ];
    }
}
