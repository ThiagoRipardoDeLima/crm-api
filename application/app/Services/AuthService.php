<?php

namespace App\Services;

use App\Http\Requests\LoginUser\LoginRequestDto;
use App\Http\Requests\LoginUser\RefreshTokenRequestDto;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\RefreshTokenRepository;

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
            $tokenResult = $user->createToken('API Token');

            //Gera um refresh token manualmente
            $refreshTokenRepository = app(RefreshTokenRepository::class);
            $attributes = [
                'id' => $tokenResult->token->id,
                'access_token_id' => $tokenResult->token->id,
                'revoked' => false,
                'expires_at' => now()->addDays(1)
            ];

            // Substitua o prazo de validade conforme necessário
            $refreshToken = $refreshTokenRepository->create($attributes);
            $expiration = now()->addSeconds(config('auth.expires_in'));

            return response()->json([
                'token_type' => 'Bearer',
                'expires_in' => $expiration->timestamp,
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => $refreshToken->id
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
        Auth::user()->tokens->each(function($token, $key){
            $token->revoke();
        });

        return response()->json(['Message' => 'Usuário saiu do sistema'], 200);
    }

    public function validateToken()
    {
        try {
            return response()->json(['status' => true, 'message' => 'Token válido', 'code' => 200]);
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refreshAccessToken(RefreshTokenRequestDto $request)
    {
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshToken = $refreshTokenRepository->find($request->refresh_token);

        if(!$refreshToken || $refreshToken->revoked) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        //revoke the current refresh token
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($refreshToken->access_token_id);

        //create a new refresh token
        $user = Auth::user();
        $newAccessToken = $user->createToken('API Token')->accessToken;
        $newRefreshToken = $user->createToken('API Token Refresh')->token;

        $expiration = now()->addSeconds(config('auth.expires_in'));

        return response()->json([
            'token_type' => 'Bearer',
            'expires_in' => $expiration->timestamp,
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ]);
    }
}
