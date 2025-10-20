<?php
namespace App\Infrastructure;

use App\Application\Factories\AuthControllerFactory;
use App\Application\Factories\BookControllerFactory;
use App\Infrastructure\Auth\JwtTokenService;
use Redis;
use Throwable;

class Kernel
{
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function handle(string $method, string $uri): void
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        $segments = explode('/', $path);

        try {
            if ($segments[0] === 'auth' && $method === 'POST' && ($segments[1] ?? '') === 'login') {
                $payload = json_decode(file_get_contents('php://input'), true) ?? [];
                $controller = AuthControllerFactory::create();
                $controller->login($payload);
                return;
            }

            if ($segments[0] === 'books') {
                $this->authorize();
                $controller = BookControllerFactory::create();

                if ($method === 'GET' && count($segments) === 1) {
                    $q = $_GET['q'] ?? null;
                    $q ? $controller->search($q) : $controller->index();
                } elseif ($method === 'POST' && count($segments) === 1) {
                    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
                    $controller->createBook($payload);
                } elseif ($method === 'GET' && count($segments) === 2 && is_numeric($segments[1])) {
                    $controller->show((int)$segments[1]);
                } elseif (in_array($method, ['PUT','PATCH']) && count($segments) === 2 && is_numeric($segments[1])) {
                    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
                    $controller->updateBook((int)$segments[1], $payload);
                } elseif ($method === 'DELETE' && count($segments) === 2 && is_numeric($segments[1])) {
                    $controller->destroy((int)$segments[1]);
                } else {
                    JsonResponse::error('Invalid route or method', 405);
                }
                return;
            }
            JsonResponse::error('Not found', 404);
        } catch (Throwable $e) {
            JsonResponse::error('Internal server error', 500, ['exception' => $e->getMessage()]);
        }
    }

    private function authorize(): void
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!str_starts_with($authHeader, 'Bearer ')) {
            JsonResponse::error('Unauthorized: Missing token', 401);
        }

        $token = substr($authHeader, 7);
        $jwt = new JwtTokenService();

        if (!$jwt->verify($token)) {
            JsonResponse::error('Unauthorized: Invalid token', 401);
        }
    }
}
