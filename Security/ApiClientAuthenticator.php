<?php

namespace RetailCrm\ServiceBundle\Security;

use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiClientAuthenticator extends AbstractClientAuthenticator
{
    public function __construct(
        ErrorJsonResponseFactory $errorResponseFactory,
        private Security $security,
    ) {
        parent::__construct($errorResponseFactory);
    }

    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        return $request->request->has(static::AUTH_FIELD);
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get(static::AUTH_FIELD);
        if (null === $identifier) {
            throw new AuthenticationException('Request does not contain authentication data');
        }

        return new SelfValidatingPassport(
            new UserBadge(
                $identifier
            ),
            []
        );
    }
}
