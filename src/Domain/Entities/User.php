<?php
namespace App\Domain\Entities;

class User
{
    public ?int $id;
    public string $email;
    public string $password;

    public function __construct(?int $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
