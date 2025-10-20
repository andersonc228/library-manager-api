<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\BookRepositoryInterface;

class ListBooks
{
    private BookRepositoryInterface $repo;

    public function __construct(BookRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function execute(): array
    {
        return $this->repo->getAll();
    }
}
