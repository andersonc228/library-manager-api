<?php
namespace App\Application\UseCases;

use App\Application\Repositories\BookRepository;
use App\Domain\Entities\Book;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;
use App\Infrastructure\Validator;
use RuntimeException;

class CreateBook
{
    private BookRepository $repo;
    private Logger $logger;
    private OpenLibraryClient $ol;

    public function __construct(BookRepository $repo, Logger $logger, OpenLibraryClient $ol)
    {
        $this->repo = $repo;
        $this->logger = $logger;
        $this->ol = $ol;
    }

    public function execute(array $data): Book
    {
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => 'isbn|required',
            'year' => 'int'
        ]);

        if ($validator->fails()) {
            throw new RuntimeException(json_encode($validator->errors()), 422);
        }

        if ($this->repo->existsByIsbn($data['isbn'])) {
            throw new RuntimeException(json_encode(['isbn' => ['ISBN must be unique']]), 422);
        }

        $book = new Book($data);
        $meta = $this->ol->fetchByISBN($book->isbn);
        $book->description = $meta['description'] ?? $book->description;
        $book->cover_url = $meta['cover_url'] ?? $book->cover_url;

        $created = $this->repo->create($book);
        $this->logger->info('Book created', ['id' => $created->id]);

        return $created;
    }
}
