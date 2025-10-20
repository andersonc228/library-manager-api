<?php
namespace App\Application\Interfaces;

use App\Domain\Entities\Book;

interface BookRepositoryInterface
{
    public function create(Book $book): Book;
    public function update(Book $book): ?Book;
    public function find(int $id): ?Book;
    public function getAll(): array;
    public function delete(int $id): bool;
    public function search(string $query): array;
    public function existsByIsbn(string $isbn, ?int $ignoreId = null): bool;


}
