<?php
namespace Tests;

use App\Application\Repositories\BookRepository;
use App\Application\UseCases\CreateBook;
use App\Domain\Entities\Book;
use App\Infrastructure\Database;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;
use PHPUnit\Framework\TestCase;

class CreateBookUseCaseTest extends TestCase
{
    private BookRepository $repo;
    private Logger $logger;
    private OpenLibraryClient $ol;

    protected function setUp(): void
    {
        putenv('DB_DATABASE=books_db_test');
        $pdo = Database::getInstance(true, true);
        $pdo->exec("TRUNCATE TABLE books");

        $this->repo = new BookRepository($pdo);
        $this->logger = new Logger();
        $this->ol = new OpenLibraryClient();
    }


    public function testCreateBookSuccessfully(): void
    {
        $data = [
            'title'=>'Test Book',
            'author'=>'Test',
            'isbn'=>'123',
            'year'=>2025
        ];

        $useCase = new CreateBook($this->repo, $this->logger, $this->ol);
        $useCase->execute($data);

        $this->assertTrue($this->repo->existsByIsbn('123'));
    }

    public function testCreateBookFailsWithDuplicateIsbn(): void
    {
        $this->repo->create(new Book(['title'=>'A','author'=>'B','isbn'=>'111']));
        $this->expectException(\RuntimeException::class);

        $useCase = new CreateBook($this->repo, $this->logger, $this->ol);
        $useCase->execute(['title'=>'xx','author'=>'B','isbn'=>'111']);
    }

}
