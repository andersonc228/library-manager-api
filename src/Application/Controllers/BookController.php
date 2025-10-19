<?php
namespace App\Application\Controllers;

use App\Application\Repositories\BookRepository;
use App\Application\UseCases\CreateBook;
use App\Application\UseCases\DeleteBook;
use App\Application\UseCases\ListBooks;
use App\Application\UseCases\SearchBooks;
use App\Application\UseCases\ShowBook;
use App\Application\UseCases\UpdateBook;
use App\Infrastructure\JsonResponse;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;
use Throwable;

class BookController
{
    private BookRepository $repo;
    private Logger $logger;
    private OpenLibraryClient $ol;

    public function __construct(
        BookRepository $repo,
        Logger $logger,
        OpenLibraryClient $ol
    ) {
        $this->repo = $repo;
        $this->logger = $logger;
        $this->ol = $ol;
    }

    public function index(): void
    {
        try {
            $books = (new ListBooks($this->repo))->execute();
            JsonResponse::success(array_map(fn($b) => $b->toArray(), $books));
        } catch (Throwable $e) {
            JsonResponse::error('Internal server error', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function search(string $term): void
    {
        try {
            $books = (new SearchBooks($this->repo))->execute($term);
            JsonResponse::success(array_map(fn($b) => $b->toArray(), $books));
        } catch (Throwable $e) {
            JsonResponse::error('Internal server error', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function show(int $id): void
    {
        try {
            $book = (new ShowBook($this->repo))->execute($id);
            JsonResponse::success($book->toArray());
        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            JsonResponse::error($e->getMessage(), $code);
        }
    }

    public function createBook(array $data): void
    {
        try {
            $book = (new CreateBook($this->repo, $this->logger, $this->ol))->execute($data);
            JsonResponse::success($book->toArray(), 201);
        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            $message = $e->getCode() === 422 ? 'Validation failed' : 'Internal server error';
            JsonResponse::error($message, $code, $this->tryDecode($e->getMessage()));
        }
    }

    public function updateBook(int $id, array $data): void
    {
        try {
            $book = (new UpdateBook($this->repo, $this->logger, $this->ol))->execute($id, $data);
            JsonResponse::success($book->toArray());
        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            JsonResponse::error($e->getMessage(), $code, $this->tryDecode($e->getMessage()));
        }
    }

    public function destroy(int $id): void
    {
        try {
            (new DeleteBook($this->repo, $this->logger))->execute($id);
            JsonResponse::success(['success' => true]);
        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            JsonResponse::error($e->getMessage(), $code);
        }
    }

    private function tryDecode(string $msg): ?array
    {
        $decoded = json_decode($msg, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
