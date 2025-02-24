<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UserRequestsDTO",
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
class UserRequestsDTO extends FormRequest
{

    private $name;
    private $email;
    private $password;

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
            'email' => 'required|email|unique:users,email,'.$this->id,
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
            'email.unique' => 'O email já esta em uso.',
            'password.required' => 'O campo password é obrigatório.',
            'password.min' => 'A senha deve conter pelo menos 6 caracteres.',
            'remember.required' => 'O campo remember é obrigatório.',
            'remember.boolean' => 'O campo remember deve ser verdadeiro ou falso.',
        ];
    }

    public static function FromModel(User $user): UserRequestsDTO
    {
        return self::getFromModel($user);
    }

    private function getFromModel(User $user)
    {
        $this->name = $user->name;
        $this->email = $user->email;
        return $this;
    }

    public static function ToModel(UserRequestsDTO $dto): User
    {
        $user = new User;
        $user->name = $dto->name;
        $user->email = $dto->email;
        $user->password = $dto->password;
        $user->remember = $dto->remember;
        return $user;
    }

}
