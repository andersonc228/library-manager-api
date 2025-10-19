<?php
namespace App\Infrastructure\Auth;

use App\Domain\Entities\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService
{
    private string $secret = 'super_secret_key';

    public function generate(User $user): string
    {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verify(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->secret, 'HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}