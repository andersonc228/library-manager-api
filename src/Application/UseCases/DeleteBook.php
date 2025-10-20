<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\BookRepositoryInterface;
use App\Infrastructure\Logger;
use RuntimeException;

class DeleteBook
{
    private BookRepositoryInterface $repo;
    private Logger $logger;

    public function __construct(BookRepositoryInterface $repo, Logger $logger)
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
