<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\BookRepositoryInterface;
use RuntimeException;

class ShowBook
{
    private BookRepositoryInterface $repo;

    public function __construct(BookRepositoryInterface $repo)
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
