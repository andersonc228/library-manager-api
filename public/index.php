<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Kernel;

$kernel = Kernel::getInstance();
$kernel->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);


