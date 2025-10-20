<?php

namespace App\Application\Controllers;

use App\Infrastructure\JsonResponse;
use App\Infrastructure\Logger;
use Throwable;

abstract class ApiController
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    protected function handle(callable $action, string $contextMessage = 'Unhandled exception'): void
    {
        try {
            $result = $action();

            if ($result === null) {
                JsonResponse::success(['success' => true]);
                return;
            }

            if (is_object($result) && method_exists($result, 'toArray')) {
                $result = $result->toArray();
            }

            if (!is_array($result) && !is_string($result)) {
                $result = (string) $result;
            }

            JsonResponse::success($result);

        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            $isValidationError = $code === 422;

            $this->logger->error($contextMessage, [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $code,
            ]);

            $decoded = $this->tryDecode($e->getMessage());

            if ($isValidationError) {
                JsonResponse::error(
                    'Validation failed',
                    422,
                    $decoded ?? ['error' => $e->getMessage()]
                );
            } else {
                JsonResponse::error(
                    'Internal server error',
                    $code,
                    ['error' => $e->getMessage()]
                );
            }
        }
    }

    private function tryDecode(string $msg): ?array
    {
        $decoded = json_decode($msg, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
