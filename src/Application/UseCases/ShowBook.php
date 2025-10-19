<?php
namespace App\Application\UseCases;

use App\Application\Repositories\BookRepository;
use RuntimeException;

class ShowBook
{
    private BookRepository $repo;

    public function __construct(BookRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(int $id)
    {
        $book = $this->repo->find($id);
        if (!$book) {
            throw new RuntimeException('Book not found', 404);
        }
        return $book;
    }
}
