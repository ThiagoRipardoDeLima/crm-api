<?php
namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Services\Interfaces\TokenRedisInterface;

class TokenRedisService implements TokenRedisInterface {

    public function store($jwtId, $token)
    {
        Redis::set("TokenId:{$jwtId}:jwt", $token);
        Redis::expire("TokenId:{$jwtId}:jwt", config('auth.jwt_expiry'));
    }

    public function get($jwtId)
    {
        Redis::get("TokenId:{$jwtId}:jwt");
    }

    public function remove($jwtId)
    {
        Redis::del("TokenId:{$jwtId}:jwt");
    }

}
