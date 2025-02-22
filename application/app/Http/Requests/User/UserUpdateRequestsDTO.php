<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UserUpdateRequestsDTO",
 *     type="object",
 *     required ={"name","email","password","remember_token"})
 *     {
 *        @OA\Property(property="name", type="string", description="User name"),
 *        @OA\Property(property="email", type="string", description="User email"),
 *        @OA\Property(property="password", type="string", description="User password"),
 *        @OA\Property(property="remember_token", type="string", description="User remember token")
 *    }
 * )
 */
class UserUpdateRequestsDTO extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules ()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'remember' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo name é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email informado é inválido.',
            'password.required' => 'O campo password é obrigatório.',
            'password.min' => 'A senha deve conter pelo menos 6 caracteres.',
            'remember.required' => 'O campo remember é obrigatório.',
            'remember.boolean' => 'O campo remember deve ser verdadeiro ou falso.',
        ];
    }
}
