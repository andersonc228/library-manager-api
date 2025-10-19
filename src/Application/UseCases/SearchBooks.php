<?php
namespace App\Application\UseCases;

use App\Application\Repositories\BookRepository;

class SearchBooks
{
    private BookRepository $repo;

    public function __construct(BookRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $term): array
    {
        return $this->repo->search($term);
    }
}
