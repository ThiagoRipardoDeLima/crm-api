<?php

namespace App\Http\Requests\ResetPassword;

use Illuminate\Foundation\Http\FormRequest;

/**
 *  @OA\Schema(
 *      schema="ResetPasswordTokenDto",
 *      type="object",
 *      required={"email"},
 *      @OA\Property(property="email", type="string", description="User's email"),
 * )
 */
class ResetPasswordTokenDto extends FormRequest {

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O e-mail fornecido não é válido',
            'email.exists' => 'O e-mail fornecido não está cadastrado',
        ];
    }
}
