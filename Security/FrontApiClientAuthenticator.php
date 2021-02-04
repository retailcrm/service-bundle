<?php

namespace RetailCrm\ServiceBundle\Security;

use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class FrontApiClientAuthenticator extends AbstractClientAuthenticator
{
    private $security;

    public function __construct(
        ErrorJsonResponseFactory $errorResponseFactory,
        Security $security
    ) {
        parent::__construct($errorResponseFactory);

        $this->security = $security;
    }

    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        return true;
    }

    public function supportsRememberMe(): bool
    {
        return true;
    }
}
