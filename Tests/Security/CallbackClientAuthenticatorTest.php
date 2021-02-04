<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class CallbackClientAuthenticatorTest extends TestCase
{
    public function testStart(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $errorResponseFactory
            ->expects(static::once())
            ->method('create')
            ->willReturn(
                new JsonResponse(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED)
            );

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->start(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
    }

    public function testGetCredentials(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->getCredentials(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertEquals('123', $result);

        $result = $auth->getCredentials(new Request([CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertEquals('123', $result);
    }

    public function testCheckCredentials(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $user = new class implements UserInterface {
            public function getRoles(): array
            {
                return ["USER"];
            }

            public function getPassword(): string
            {
                return "123";
            }

            public function getSalt(): string
            {
                return "salt";
            }

            public function getUsername(): string
            {
                return "user";
            }

            public function eraseCredentials(): void
            {
            }
        };

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->checkCredentials(new Request(), $user);

        static::assertTrue($result);
    }

    public function testOnAuthenticationFailure(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $errorResponseFactory
            ->expects(static::once())
            ->method('create')
            ->willReturn(
                new JsonResponse(
                    ['message' => 'An authentication exception occurred.'],
                    Response::HTTP_FORBIDDEN
                )
            );

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->start(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function testSupports(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->supports(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request([CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request());

        static::assertFalse($result);
    }

    public function testSupportsRememberMe(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->supportsRememberMe();

        static::assertFalse($result);
    }
}
