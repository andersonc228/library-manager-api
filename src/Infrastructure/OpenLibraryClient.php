<?php
namespace App\Infrastructure;

use Exception;
use Redis;

class OpenLibraryClient
{
    private string $baseUrl = 'https://openlibrary.org/api/books';
    private Redis $redis;
    private int $rateLimit = 5;

    public function __construct(?Redis $redis = null)
    {
        $this->redis = $redis ?? new Redis();
        if (!$redis) {
            $this->redis->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
        }
    }

    public function fetchByISBN(string $isbn): array
    {
        $cacheKey = "ol:isbn:$isbn";

        $cached = $this->redis->get($cacheKey);
        if ($cached) {
            return json_decode($cached, true);
        }

        $this->checkRateLimit();

        $url = $this->baseUrl . "?bibkeys=ISBN:$isbn&format=json&jscmd=data";
        $response = @file_get_contents($url);
        if (!$response) {
            throw new Exception("Failed to fetch data from OpenLibrary");
        }

        $data = json_decode($response, true)["ISBN:$isbn"] ?? [];

        $this->redis->setex($cacheKey, 3600, json_encode($data));

        return $data;
    }

    private function checkRateLimit(): void
    {
        $key = "ol:rate_limit";
        $current = $this->redis->get($key) ?: 0;

        if ($current >= $this->rateLimit) {
            throw new Exception("Rate limit exceeded");
        }

        $this->redis->incr($key);
        $this->redis->expire($key, 1);
    }
}
