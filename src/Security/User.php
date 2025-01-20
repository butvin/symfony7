<?php

declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, JWTUserInterface
{
    private string $username;
    private array $roles;

    public function __construct(string $username, array $roles)
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
       return $this->username;
    }

    public static function createFromPayload($username, array $payload): JWTUserInterface|User
    {
        return new self($username, $payload['roles']);
    }
}