<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\BookRepositoryInterface;

class SearchBooks
{
    private BookRepositoryInterface $repo;

    public function __construct(BookRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $term): array
    {
        return $this->repo->search($term);
    }
}
