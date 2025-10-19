<?php
namespace App\Infrastructure;

use App\Application\Factories\AuthControllerFactory;
use App\Application\Factories\BookControllerFactory;
use App\Infrastructure\Auth\JwtTokenService;
use Throwable;

final class Kernel
{
    private static ?self $instance = null;
    private JwtTokenService $jwt;

    private function __construct()
    {
        $this->jwt = new JwtTokenService();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function handle(string $method, string $uri): void
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        $segments = explode('/', $path);

        try {
            if ($this->isAuthRoute($segments, $method)) {
                $this->handleAuth($segments);
                return;
            }

            if ($this->isBooksRoute($segments)) {
                $this->handleBooks($method, $segments);
                return;
            }

            JsonResponse::error('Not found', 404);

        } catch (Throwable $e) {
            JsonResponse::error('Internal server error', 500, [
                'message' => $e->getMessage()
            ]);
        }
    }

    private function isAuthRoute(array $segments, string $method): bool
    {
        return $segments[0] === 'auth' && $method === 'POST' && ($segments[1] ?? '') === 'login';
    }

    private function handleAuth(array $segments): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller = AuthControllerFactory::create();
        $controller->login($payload);
    }

    private function isBooksRoute(array $segments): bool
    {
        return $segments[0] === 'books';
    }

    private function handleBooks(string $method, array $segments): void
    {
        $this->authorize();
        $controller = BookControllerFactory::create();

        switch (true) {
            case $method === 'GET' && count($segments) === 1:
                $q = $_GET['q'] ?? null;
                $q ? $controller->search($q) : $controller->index();
                break;

            case $method === 'POST' && count($segments) === 1:
                $payload = json_decode(file_get_contents('php://input'), true) ?? [];
                $controller->createBook($payload);
                break;

            case $method === 'GET' && count($segments) === 2 && is_numeric($segments[1]):
                $controller->show((int)$segments[1]);
                break;

            case in_array($method, ['PUT', 'PATCH'], true) && count($segments) === 2 && is_numeric($segments[1]):
                $payload = json_decode(file_get_contents('php://input'), true) ?? [];
                $controller->updateBook((int)$segments[1], $payload);
                break;

            case $method === 'DELETE' && count($segments) === 2 && is_numeric($segments[1]):
                $controller->destroy((int)$segments[1]);
                break;

            default:
                JsonResponse::error('Invalid route or method', 405);
        }
    }

    private function authorize(): void
    {
        $authHeader = '';

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) {
                $authHeader = $headers['authorization'];
            }
        }

        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            JsonResponse::error('Unauthorized: Missing token', 401);
        }

        $token = substr($authHeader, 7);
        $jwt = new JwtTokenService();

        if (!$jwt->verify($token)) {
            JsonResponse::error('Unauthorized: Invalid token', 401);
        }
    }

}
