<?php
namespace App\Domain\Entities;

class Book
{
    public ?int $id;
    public string $title;
    public string $author;
    public ?string $isbn;
    public ?int $year;
    public ?string $description;
    public ?string $cover_url;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->author = $data['author'] ?? '';
        $this->isbn = $data['isbn'] ?? null;
        $this->year = isset($data['year']) ? (int)$data['year'] : null;
        $this->description = $data['description'] ?? null;
        $this->cover_url = $data['cover_url'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'year' => $this->year,
            'description' => $this->description,
            'cover_url' => $this->cover_url,
        ];
    }
}
