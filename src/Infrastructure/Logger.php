<?php
namespace App\Infrastructure;

class Logger
{
    private string $file;

    public function __construct(string $file = __DIR__ . '/../../storage/app.log')
    {
        $this->file = $file;

        if (!file_exists($this->file)) {
            touch($this->file);
            chmod($this->file, 0666);
        }

    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $entry = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );

        @file_put_contents($this->file, $entry, FILE_APPEND);
    }
}
