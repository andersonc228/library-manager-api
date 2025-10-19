<?php
namespace App\Application\UseCases\Auth;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Auth\JwtTokenService;

class LoginUseCase
{
    private UserRepositoryInterface $userRepo;
    private JwtTokenService $tokenService;

    public function __construct(UserRepositoryInterface $userRepo, JwtTokenService $tokenService)
    {
        $this->userRepo = $userRepo;
        $this->tokenService = $tokenService;
    }

    public function execute(string $email, string $password): string
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            throw new \RuntimeException('Invalid credentials');
        }

        return $this->tokenService->generate($user);
    }
}
