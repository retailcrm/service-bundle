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

/**
 * Class AbstractClientAuthenticator
 *
 * @package RetailCrm\ServiceBundle\Security
 */
abstract class AbstractClientAuthenticator extends AbstractGuardAuthenticator
{
    public const AUTH_FIELD = 'clientId';

    private $errorResponseFactory;

    /**
     * AbstractClientAuthenticator constructor.
     *
     * @param ErrorJsonResponseFactory $errorResponseFactory
     */
    public function __construct(ErrorJsonResponseFactory $errorResponseFactory)
    {
        $this->errorResponseFactory = $errorResponseFactory;
    }

    /**
     * {@inheritdoc }
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $error = new Error();
        $error->message = 'Authentication required';

        return $this->errorResponseFactory->create($error,Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc }
     */
    public function getCredentials(Request $request): string
    {
        return $request->get(static::AUTH_FIELD);
    }

    /**
     * {@inheritdoc }
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    /**
     * {@inheritdoc }
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * {@inheritdoc }
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $error = new Error();
        $error->message = $exception->getMessageKey();

        return $this->errorResponseFactory->create($error,Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritdoc }
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }
}
