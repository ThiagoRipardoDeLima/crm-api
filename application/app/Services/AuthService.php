<?php

namespace App\Services;

use App\Http\Requests\LoginUser\LoginRequestDto;
use App\Http\Requests\LoginUser\RefreshTokenRequestDto;
use App\Http\Requests\ResetPassword\ResetPasswordTokenDto;
use App\Http\Requests\UpdatePassword\UpdatePasswordRequestDto;
use App\Notification\ResetPasswordNotification;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Support\Str;


class AuthService
{

    protected $tokenRedisService;
    protected $authRepository;

    public function __construct(TokenRedisService $tokenRedisService, AuthRepository $authRepository) {
        $this->tokenRedisService = $tokenRedisService;
        $this->authRepository = $authRepository;
    }

    public function authenticate(LoginRequestDto $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($credentials)){
            //create a new token
            $tokenResult = $this->createToken('API Token');

            // Novo
            $jti = $this->carregarJtiToken($tokenResult->accessToken);
            if($jti)
                $this->tokenRedisService->store($jti, $tokenResult->accessToken);

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

    public function logout()
    {
        Auth::user()->tokens->each(function($token, $key){
            $jti = $this->carregarJtiToken($token);
            if($jti)
                $this->tokenRedisService->remove($jti);

            $token->revoke();
        });

        return response()->json(['Message' => 'Usuário saiu do sistema'], 200);
    }

    public function refreshAccessToken(RefreshTokenRequestDto $request)
    {
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshToken = $refreshTokenRepository->find($request->refresh_token);

        if(!$refreshToken || $refreshToken->revoked)
            return response()->json(['error' => 'Invalid refresh token'], 401);

        //revoke the current refresh token
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($refreshToken->access_token_id);
        //create a new refresh token
        $newRefreshToken = $this->createToken('API Token Refresh');

        $jti = $this->carregarJtiToken($newRefreshToken->accessToken);
        if($jti)
            $this->tokenRedisService->store($jti, $newRefreshToken->accessToken);

        $expiration = now()->addSeconds(config('auth.expires_in'));

        return response()->json([
            'token_type' => 'Bearer',
            'expires_in' => $expiration->timestamp,
            'access_token' => $newRefreshToken->accessToken,
            'refresh_token' => $newRefreshToken->token->id
        ]);
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

    public function updatePassword(UpdatePasswordRequestDto $request)
    {
        return $this->authRepository->updatePassword($request);
    }

    public function handleResetPasswordResquest($email)
    {
        $user = $this->authRepository->findByEmail($email);
        if(!$user)
            return response()->json(['message' => 'Email não encontrado'], 404);

        // Gere um token (por simplicidade, vamos usar o Str::random)
        $token = Str::random(60);
        $hashedToken = Hash::make($token);

        // Salva o token na base de dados (supomos que você tem um campo 'password_reset_token' no modelo User)
        $this->authRepository->setResetToken($user, $hashedToken);

        $user->notify(new ResetPasswordNotification($hashedToken));

        return response()->json(['message' => 'E-mail de redefinição de senha enviado com sucesso!']);

    }

    public function resetByToken(ResetPasswordTokenDto $request)
    {
        return $this->authRepository->resetPasswordByToken($request);
    }

    private function carregarJtiToken($token)
    {
        $decodedPayload = decode_jwt($token);
        return ($decodedPayload && isset($decodedPayload->jti)) ? $decodedPayload->jti : false;
    }

    private function createToken(string $nameToken): object
    {
        return Auth::user()->createToken($nameToken);
    }
}
