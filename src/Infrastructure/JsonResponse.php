<?php
namespace App\Infrastructure;

class JsonResponse
{
    public static function success(array|string $data, int $status = 200): void
    {
        self::send($data, $status);
    }

    public static function error(string $message, int $status = 400, array $context = []): void
    {
        self::send([
            'error' => $message,
            'context' => !empty($context) ? $context : null
        ], $status);
    }

    private static function send(array|string $data, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        if (is_string($data)) {
            $data = ['message' => $data];
        }

        if (is_array($data)) {
            $data = ['data' => $data];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
