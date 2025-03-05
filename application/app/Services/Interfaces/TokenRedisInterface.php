<?php
namespace App\Services\Interfaces;

interface TokenRedisInterface {
    public function store($jwtId, $token);
    public function get($jwtId);
    public function remove($jwtId);
}
