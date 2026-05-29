<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Override;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    public function messages() : array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.mixed' => 'La contraseña debe tener al menos 1 letra mayúscula y 1 minúscula',
            'password.symbols' => 'La contraseña debe tener al menos 1 caracter especial (^@_-.*)',
            'password.numbers' => 'La contraseña debe tener al menos 1 número',
            'password.uncompromised' => 'La contraseña está comprometida, elige una más segura'
            
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 
                Password::min(8)
                ->mixedCase()
                ->symbols()
                ->numbers()
                ->uncompromised()
            ]
        ];
    }
}
