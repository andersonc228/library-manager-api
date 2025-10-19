<?php
namespace App\Application\UseCases;

use App\Application\Repositories\BookRepository;

class ListBooks
{
    private BookRepository $repo;

    public function __construct(BookRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(): array
    {
        return $this->repo->all();
    }
}
