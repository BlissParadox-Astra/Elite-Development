<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $rules = [
            'barcode' => [
                'nullable',
                'string',
                'max:39',
            ],
            'description' => [
                'required',
                'string',
                'max:60',
                'min:5',
            ],
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0',
            'reorder_level' => 'required|integer|min:1',
            'stock_on_hand' => 'nullable|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
        ];

        if ($this->isMethod('POST')) {
            $rules['barcode'][] = 'unique:products,barcode';
            $rules['description'][] = 'unique:products,description';
        }

        return $rules;
    }
}
