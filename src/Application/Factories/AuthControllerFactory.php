<?php
namespace App\Application\Factories;

use App\Application\Controllers\AuthController;

class AuthControllerFactory
{
    public static function create(): AuthController
    {
        return new AuthController();
    }
}
