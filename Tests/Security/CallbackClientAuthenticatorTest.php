<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use RetailCrm\ServiceBundle\Tests\DataFixtures\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CallbackClientAuthenticatorTest extends TestCase
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

        $userRepository = $this->createMock(ObjectRepository::class);
        $auth = new CallbackClientAuthenticator($errorResponseFactory, $userRepository);
        $result = $auth->onAuthenticationFailure(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function testSupports(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $userRepository = $this->createMock(ObjectRepository::class);
        $auth = new CallbackClientAuthenticator($errorResponseFactory, $userRepository);
        $result = $auth->supports(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request([CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request());

        static::assertFalse($result);
    }

    public function testAuthenticate(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $user = new User();

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->willReturn($user)
        ;

        $auth = new CallbackClientAuthenticator($errorResponseFactory, $userRepository);

        $passport = $auth->authenticate(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));
        $authUser = $passport->getUser();
        static::assertEquals($user, $authUser);

        $this->expectException(AuthenticationException::class);
        $auth->authenticate(new Request());
    }

    public function testOnAuthenticationSuccess(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);
        $userRepository = $this->createMock(ObjectRepository::class);
        $auth = new CallbackClientAuthenticator($errorResponseFactory, $userRepository);

        $result = $auth->onAuthenticationSuccess($request, $token, 'key');

        static::assertNull($result);
    }
}
