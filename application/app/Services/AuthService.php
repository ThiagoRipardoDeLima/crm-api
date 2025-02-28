<?php

namespace App\Services;

use App\Http\Requests\LoginRequestDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthService
{

    public function authenticate(LoginRequestDto $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('API Token')->accessToken;
            $expiration = now()->addSeconds(config('auth.expires_in'));

            return response()->json([
                'token_type' => 'Bearer',
                'expires_in' => $expiration->timestamp,
                'access_token' => $token
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    public function register(array $data)
    {
        /* $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user; */
    }

    public function login(array $credentials)
    {
        /* if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return ['user' => $user, 'token' => $token];
        }

        return ['error' => 'Unauthorized']; */
    }

    public function logout()
    {

        dd(Auth::user());
        Auth::user()->tokens->each(function($token, $key){
            $token->revoke();
        });

        return response()->json(['Message' => 'Usuário saiu do sistema'], 200);
    }
}
