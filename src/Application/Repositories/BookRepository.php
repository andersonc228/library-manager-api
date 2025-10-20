<?php
namespace App\Application\Repositories;

use App\Application\Interfaces\BookRepositoryInterface;
use App\Domain\Entities\Book;
use PDO;

class BookRepository implements BookRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(Book $book): Book
    {
        $stmt = $this->db->prepare(
            "INSERT INTO books(title,author,isbn,year,description,cover_url)
             VALUES(:title,:author,:isbn,:year,:description,:cover_url)"
        );
        $stmt->execute([
            ':title'=> $book->title,
            ':author'=> $book->author,
            ':isbn'=> $book->isbn,
            ':year'=> $book->year,
            ':description'=> $book->description,
            ':cover_url'=> $book->cover_url
        ]);

        $book->id = (int) $this->db->lastInsertId();
        return $book;
    }

    public function update(Book $book): ?Book
    {
        if (!$book->id) return null;

        $stmt = $this->db->prepare(
            "UPDATE books SET title=:title, author=:author, isbn=:isbn, year=:year, description=:description, cover_url=:cover_url WHERE id=:id"
        );
        $stmt->execute([
            ':title'=> $book->title,
            ':author'=> $book->author,
            ':isbn'=> $book->isbn,
            ':year'=> $book->year,
            ':description'=> $book->description,
            ':cover_url'=> $book->cover_url,
            ':id'=> $book->id
        ]);

        return $book;
    }

    public function find(int $id): ?Book
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id=:id");
        $stmt->execute([':id'=> $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new Book($result) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id=:id");

        return $stmt->execute([':id'=>$id]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM books ORDER BY id DESC");

        return array_map(fn($r) => new Book($r), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function search(string $q): array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE title LIKE :q OR author LIKE :q");
        $stmt->execute([':q'=> "%$q%"]);

        return array_map(fn($r) => new Book($r), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function existsByIsbn(string $isbn, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM books WHERE isbn = :isbn";
        $params = [':isbn' => $isbn];

        if ($ignoreId) {
            $sql .= " AND id != :id";
            $params[':id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }
}
