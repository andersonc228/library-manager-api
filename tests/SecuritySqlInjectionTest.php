<?php
namespace Tests;

use App\Application\Repositories\BookRepository;
use App\Domain\Entities\Book;
use App\Infrastructure\Database;
use PDO;
use PHPUnit\Framework\TestCase;

class SecuritySqlInjectionTest extends TestCase
{
    private BookRepository $repo;
    private PDO $pdo;

    protected function setUp(): void
    {
        putenv('DB_DATABASE=books_db_test');
        $this->pdo = Database::getInstance(true, true);
        $this->pdo->exec("TRUNCATE TABLE books");
        $this->repo = new BookRepository($this->pdo);
    }

    public function testSqlInjectionInsertsDoNotBreakSchema(): void
    {
        $maliciousIsbn = "123'; DROP TABLE books; --";
        $book = new Book([
            'title' => $maliciousIsbn,
            'author' => 'Evil',
            'isbn' => '1234567890',
        ]);

        $created = $this->repo->create($book);
        $this->assertNotNull($created->id);

        $stmt = $this->pdo->query("SHOW TABLES LIKE 'books'");
        $this->assertNotFalse($stmt->fetch());
    }
}
