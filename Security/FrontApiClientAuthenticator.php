<?php

namespace RetailCrm\ServiceBundle\Security;

use App\Repository\ConnectionRepository;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FrontApiClientAuthenticator extends AbstractClientAuthenticator
{
    private $security;
    private $repository;

    public function __construct(
        ErrorJsonResponseFactory $errorResponseFactory,
        Security $security,
        ConnectionRepository $repository
    ) {
        parent::__construct($errorResponseFactory);

        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc }
     */
    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        return $request->request->has(static::AUTH_FIELD);
    }

    /**
     * {@inheritdoc }
     */
    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get(static::AUTH_FIELD);

        return new SelfValidatingPassport(
            new UserBadge($identifier, function ($userIdentifier) {
                return $this->repository->findByIdentifier($userIdentifier);
            }),
            [new RememberMeBadge()]
        );
    }
}
