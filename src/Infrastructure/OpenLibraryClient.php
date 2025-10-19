<?php
namespace App\Infrastructure;

class OpenLibraryClient
{
    public function fetchByISBN(string $isbn): array
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);
        if (empty($isbn)) return [];

        $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
        $response = $this->httpGet($url);

        if (!$response) return [];

        $data = json_decode($response, true);
        $key = "ISBN:{$isbn}";
        if (!isset($data[$key])) return [];

        $book = $data[$key];
        $desc = $book['description']['value'] ?? $book['description'] ?? null;
        $cover = $book['cover']['medium'] ?? $book['cover']['large'] ?? null;

        return ['description' => $desc, 'cover_url' => $cover];
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'LibraryManager/1.0'
        ]);
        $res = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);

        return $err ? null : $res;
    }
}
