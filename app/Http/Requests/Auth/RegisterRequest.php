<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'min:1'],
            'email'      => ['required', 'email', 'unique:users'],
            'role'       => ['required', 'string', 'in:user,admin'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'admin_code' => ['nullable', 'string'], // 👈 السطر ده هو اللي كان ناقص وموقف السيستم بالكامل
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required'       => 'Name is required',
            'name.string'         => 'Name must be a string',
            'name.min'            => 'Name must be at least 1 character',
            'email.required'      => 'Email is required',
            'email.email'         => 'Email must be a valid email address',
            'email.unique'        => 'Email already exists',
            'role.required'       => 'Role is required',
            'role.in'             => 'Role must be either user or admin',
            'password.required'   => 'Password is required',
            'password.string'     => 'Password must be a string',
            'password.min'        => 'Password must be at least 6 characters',
            'password.confirmed'  => 'Password confirmation does not match',
        ];
    }
}