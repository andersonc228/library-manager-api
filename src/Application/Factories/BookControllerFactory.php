<?php
namespace App\Application\Factories;

use App\Application\Controllers\BookController;
use App\Application\Repositories\BookRepository;
use App\Infrastructure\Database;
use App\Infrastructure\Logger;
use App\Infrastructure\OpenLibraryClient;

class BookControllerFactory
{
    public static function create(): BookController
    {
        $pdo = Database::getInstance();
        $repo = new BookRepository($pdo);
        $logger = new Logger();
        $ol = new OpenLibraryClient();

        return new BookController($repo, $logger, $ol);
    }
}
