<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\BookRepositoryInterface;
use App\Domain\Entities\Book;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;
use App\Infrastructure\Validator;
use RuntimeException;

class UpdateBook
{
    private BookRepositoryInterface $repo;
    private Logger $logger;
    private OpenLibraryClient $ol;

    public function __construct(BookRepositoryInterface $repo, Logger $logger, OpenLibraryClient $ol)
    {
        $this->repo = $repo;
        $this->logger = $logger;
        $this->ol = $ol;
    }

    public function execute(int $id, array $data): Book
    {
        $book = $this->repo->find($id);
        if (!$book) {
            throw new RuntimeException('Book not found', 404);
        }

        $validator = Validator::make($data, [
            'title' => 'string',
            'author' => 'string',
            'isbn' => 'isbn',
            'year' => 'int'
        ]);

        if ($validator->fails()) {
            throw new RuntimeException(json_encode($validator->errors()), 422);
        }

        foreach ($data as $key => $value) {
            if (property_exists($book, $key)) {
                $book->$key = $value;
            }
        }

        if (!empty($book->isbn)) {
            $meta = $this->ol->fetchByISBN($book->isbn);
            $book->description = $meta['description'] ?? $book->description;
            $book->cover_url = $meta['cover_url'] ?? $book->cover_url;
        }

        $this->repo->update($book);
        $this->logger->info('Book updated', ['id' => $book->id]);

        return $book;
    }
}
