<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'user_type_id' => 'required|exists:user_types,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
            'gender' => 'required',
            'age' => 'required|numeric',
            'address' => 'required',
            'contact_number' => [
                'required',
                'regex:/^[0-9]{11}$/'
            ],
            'username' => 'required|string|unique:user_credentials',
        ];

        if ($this->isMethod('PUT')) {
            $rules['username'] = [
                'required',
                'string',
                Rule::unique('user_credentials')->ignore($this->route('user')),
            ];
            $rules['password'] = [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'user_type_id.required' => 'The user type is required.',
            'user_type_id.exists' => 'The selected user type is invalid.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'gender.required' => 'The gender is required.',
            'age.required' => 'The age is required.',
            'age.numeric' => 'The age must be a number.',
            'address.required' => 'The address is required.',
            'contact_number.required' => 'The contact number is required.',
            'contact_number.regex' => 'The contact number must be 11 digits long and contain only numbers.',
            'username.required' => 'The username is required.',
            'username.string' => 'The username must be a string.',
            'username.unique' => 'The username has already been taken.',
            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least :min characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }
}
