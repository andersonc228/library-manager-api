<?php
namespace App\Application\Controllers;

use App\Application\Repositories\UserRepository;
use App\Application\UseCases\Auth\LoginUseCase;
use App\Infrastructure\Auth\JwtTokenService;
use App\Infrastructure\Database;
use App\Infrastructure\JsonResponse;

class AuthController
{
    public function login(array $payload): void
    {
        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;

        if (!$email || !$password) {
            JsonResponse::error('Missing credentials', 400);
        }

        $pdo = Database::getInstance();
        $userRepo = new UserRepository($pdo);
        $jwt = new JwtTokenService();

        $useCase = new LoginUseCase($userRepo, $jwt);

        try {
            $token = $useCase->execute($email, $password);
            JsonResponse::success(['token' => $token]);
        } catch (\RuntimeException $e) {
            JsonResponse::error($e->getMessage(), 401);
        }
    }
}
