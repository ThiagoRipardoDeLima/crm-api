<?php

namespace App\Repositories;

use App\Http\Requests\ResetPassword\ResetPasswordTokenDto;
use App\Http\Requests\UpdatePassword\UpdatePasswordRequestDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function updatePassword(UpdatePasswordRequestDto $request)
    {
        // 1. Verifique se a senha atual é válida
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error'=> 'Você não pode utilizar uma senha já utilizada ou igual a atual'],401);
        }

        // 2. Atualize a senha
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 3. Retorne a resposta (sucesso ou erro)
        return response()->json(['message' => 'Senha atualizada com sucesso'], 200);
    }

    public function resetPasswordByToken(ResetPasswordTokenDto $request)
    {
        // Busque o usuário pelo e-mail
        $user = User::where('email', $request->email)->first();
        if(!$user)
            throw new \Exception('Usuário não encontrado');

        // verifica se o token esta correto
        if($request->token !== $user->password_reset_token)
            throw new \Exception('Token inválido');

        // atualiza a senha do usuario
        $user->password = Hash::make($request->password);
        // limpar o token de redefinição de senha
        $user->password_reset_token = null;

        $user->save();

        return response()->json(['message' => 'Senha redefinida com sucesso'], 200);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function setResetToken($user, $token)
    {
        $user->password_reset_token = $token;
        $user->save();
    }

}
