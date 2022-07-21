<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    protected string $clientId = '123';

    public function getRoles(): array
    {
        return ["USER"];
    }

    public function getUsername(): string
    {
        return "user";
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->clientId;
    }
}
