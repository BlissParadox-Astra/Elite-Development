<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CanceledOrderRequest extends FormRequest
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
            'transaction_id' => 'required|exists:transactions,id',
            'quantity' => 'required|integer|min:1',
            'canceled_date' => 'required|date',
            'reason' => 'required',
            'action_taken' => 'required',
        ];
    }
}
