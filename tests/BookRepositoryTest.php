<?php
namespace Tests;

use App\Application\Repositories\BookRepository;
use App\Domain\Entities\Book;
use App\Infrastructure\Database;
use PHPUnit\Framework\TestCase;

class BookRepositoryTest extends TestCase
{
    private BookRepository $repo;

    protected function setUp(): void
    {
        putenv('DB_DATABASE=books_db_test');
        $pdo = Database::getInstance(true, true);
        $pdo->exec("TRUNCATE TABLE books");
        $this->repo = new BookRepository($pdo);
    }

    public function testCreateFindAndDeleteBook(): void
    {
        $book = new Book([
            'title' => 'Test Book',
            'author' => 'Anderson',
            'isbn' => '1234567890',
            'year' => 2025,
            'description' => 'Own description',
            'cover_url' => 'http://google.com'
        ]);

        $created = $this->repo->create($book);
        $this->assertNotNull($created->id);
        $this->assertIsInt($created->id);

        $found = $this->repo->find($created->id);
        $this->assertEquals($book->title, $found->title);
        $this->assertEquals($book->author, $found->author);

        $deleted = $this->repo->delete($created->id);
        $this->assertTrue($deleted);

        $this->assertNull($this->repo->find($created->id));
    }

    public function testSearchReturnsMatchingResults(): void
    {
        $this->repo->create(new Book(['title' => 'New book', 'author' => 'Test author']));
        $this->repo->create(new Book(['title' => 'Other book', 'author' => 'Own author']));;;

        $results = $this->repo->search('new');
        $this->assertCount(1, $results);
        $this->assertEquals('New book', $results[0]->title);
    }

    public function testExistsByIsbn(): void
    {
        $this->repo->create(new Book([
            'title' => 'Test ISBN',
            'author' => 'Author',
            'isbn' => '1234567890'
        ]));

        $this->assertTrue($this->repo->existsByIsbn('1234567890'));
        $this->assertFalse($this->repo->existsByIsbn('0000000000'));
    }
}
