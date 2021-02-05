<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use RetailCrm\ServiceBundle\Tests\DataFixtures\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FrontApiClientAuthenticatorTest
 *
 * @package RetailCrm\ServiceBundle\Tests\Security
 */
class FrontApiClientAuthenticatorTest extends TestCase
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
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->start(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
    }

    public function testGetCredentials(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->getCredentials(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertEquals('123', $result);

        $result = $auth->getCredentials(new Request([CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertEquals('123', $result);
    }

    public function testCheckCredentials(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->checkCredentials(new Request(), new User());

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
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->start(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function testSupportsFalse(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new User());

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->supports(new Request());

        static::assertFalse($result);
    }

    public function testSupportsTrue(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->supports(new Request([], [FrontApiClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);
    }

    public function testSupportsRememberMe(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->supportsRememberMe();

        static::assertTrue($result);
    }
}
