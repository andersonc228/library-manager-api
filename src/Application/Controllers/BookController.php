<?php

namespace App\Application\Controllers;

use App\Application\Interfaces\BookRepositoryInterface;
use App\Application\UseCases\CreateBook;
use App\Application\UseCases\DeleteBook;
use App\Application\UseCases\ListBooks;
use App\Application\UseCases\SearchBooks;
use App\Application\UseCases\ShowBook;
use App\Application\UseCases\UpdateBook;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;

class BookController extends ApiController
{
    private BookRepositoryInterface $repo;
    private OpenLibraryClient $ol;

    public function __construct(
        BookRepositoryInterface $repo,
        Logger $logger,
        OpenLibraryClient $ol
    ) {
        parent::__construct($logger);
        $this->repo = $repo;
        $this->ol = $ol;
    }

    public function index(): void
    {
        $this->handle(fn() =>
        array_map(fn($b) => $b->toArray(), (new ListBooks($this->repo))->execute()),
            'Error listing books'
        );
    }

    public function search(string $term): void
    {
        $this->handle(fn() =>
        array_map(fn($b) => $b->toArray(), (new SearchBooks($this->repo))->execute($term)),
            'Error searching books'
        );
    }

    public function show(int $id): void
    {
        $this->handle(fn() =>
        (new ShowBook($this->repo))->execute($id)->toArray(),
            "Error showing book #$id"
        );
    }

    public function createBook(array $data): void
    {
        $this->handle(fn() =>
        (new CreateBook($this->repo, $this->logger, $this->ol))->execute($data)->toArray(),
            'Error creating book'
        );
    }

    public function updateBook(int $id, array $data): void
    {
        $this->handle(fn() =>
        (new UpdateBook($this->repo, $this->logger, $this->ol))->execute($id, $data)->toArray(),
            "Error updating book #$id"
        );
    }

    public function destroy(int $id): void
    {
        $this->handle(fn() => (new DeleteBook($this->repo, $this->logger))->execute($id),
            "Error deleting book #$id"
        );
    }
}
