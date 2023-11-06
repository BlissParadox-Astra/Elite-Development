<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'brand_name' => 'required|unique:brands,brand_name',
            'category_id' => 'required|exists:categories,id',
        ];

        if ($this->isMethod('PUT')) {
            $rules = array_merge($rules, [
                'brand_name' => 'required',
            ]);
        }
        return $rules;
    }
}
