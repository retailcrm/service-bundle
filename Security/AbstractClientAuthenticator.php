<?php

namespace RetailCrm\ServiceBundle\Security;

use RetailCrm\ServiceBundle\Models\Error;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

abstract class AbstractClientAuthenticator extends AbstractGuardAuthenticator
{
    public const AUTH_FIELD = 'clientId';

    private $errorResponseFactory;

    public function __construct(ErrorJsonResponseFactory $errorResponseFactory)
    {
        $this->errorResponseFactory = $errorResponseFactory;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $error = new Error();
        $error->message = 'Authentication required';

        return $this->errorResponseFactory->create($error,Response::HTTP_UNAUTHORIZED);
    }

    public function getCredentials(Request $request): string
    {
        return $request->get(static::AUTH_FIELD);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $error = new Error();
        $error->message = $exception->getMessageKey();

        return $this->errorResponseFactory->create($error,Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }
}
