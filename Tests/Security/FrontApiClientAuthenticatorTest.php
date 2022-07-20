<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use RetailCrm\ServiceBundle\Tests\DataFixtures\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class FrontApiClientAuthenticatorTest extends TestCase
{
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
        $result = $auth->onAuthenticationFailure(new Request(), new AuthenticationException());

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

    public function testAuthenticate(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);

        $user = new User();
        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with([FrontApiClientAuthenticator::AUTH_FIELD => '123'])
            ->willReturn($user)
        ;
        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $auth->setUserRepository($userRepository);

        $passport = $auth->authenticate(new Request([], [FrontApiClientAuthenticator::AUTH_FIELD => '123']));
        $authUser = $passport->getUser();
        static::assertEquals($user, $authUser);

        $this->expectException(AuthenticationException::class);
        $auth->authenticate(new Request());
    }

    public function testOnAuthenticationSuccess(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);

        $result = $auth->onAuthenticationSuccess($request, $token, 'key');

        static::assertNull($result);
    }
}
