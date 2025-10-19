<?php
namespace App\Application\UseCases;

use App\Application\Repositories\BookRepository;
use App\Infrastructure\Logger;
use RuntimeException;

class DeleteBook
{
    private BookRepository $repo;
    private Logger $logger;

    public function __construct(BookRepository $repo, Logger $logger)
    {
        $this->repo = $repo;
        $this->logger = $logger;
    }

    public function execute(int $id): void
    {
        $deleted = $this->repo->delete($id);
        if (!$deleted) {
            throw new RuntimeException('Book not found', 404);
        }
        $this->logger->info('Book deleted', ['id' => $id]);
    }
}
