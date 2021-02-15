<?php

namespace RetailCrm\ServiceBundle\Tests\ArgumentResolver;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\ArgumentResolver\ClientValueResolver;
use RetailCrm\ServiceBundle\Exceptions\InvalidRequestArgumentException;
use RetailCrm\ServiceBundle\Serializer\SymfonySerializerAdapter;
use RetailCrm\ServiceBundle\Tests\DataFixtures\RequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Generator;

/**
 * Class ClientValueResolverTest
 *
 * @package RetailCrm\ServiceBundle\Tests\ArgumentResolver
 */
class ClientValueResolverTest extends TestCase
{
    private $resolver;

    public function setUp(): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->resolver = new ClientValueResolver(
            new SymfonySerializerAdapter($serializer, $serializer),
            Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->getValidator(),
            [
                RequestDto::class
            ]
        );
    }

    public function testSupports(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request();

        $result = $this->resolver->supports($request, $argument);

        static::assertTrue($result);
    }

    public function testNotSupports(): void
    {
        $argument = new ArgumentMetadata('RequestDto', 'NotFoundRequestDto', false, false, null);
        $request = new Request();

        $result = $this->resolver->supports($request, $argument);

        static::assertFalse($result);
    }

    public function testResolvePost(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST],
            json_encode(['param' => 'parameter'], JSON_THROW_ON_ERROR)
        );

        $result = $this->resolver->resolve($request, $argument);

        static::assertInstanceOf(Generator::class, $result);
        static::assertInstanceOf(RequestDto::class, $result->current());
        static::assertEquals('parameter', $result->current()->param);
    }

    public function testResolvePostFailure(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST],
            json_encode(['param' => null], JSON_THROW_ON_ERROR)
        );

        $this->expectException(InvalidRequestArgumentException::class);

        $result = $this->resolver->resolve($request, $argument);
        $result->current();
    }

    public function testResolveGet(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            ['param' => 'parameter'],
            [],
            [],
            [],
            [],
            []
        );

        $result = $this->resolver->resolve($request, $argument);

        static::assertInstanceOf(Generator::class, $result);
        static::assertInstanceOf(RequestDto::class, $result->current());
        static::assertEquals('parameter', $result->current()->param);
    }

    public function testResolveGetFailure(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            ['param' => null],
            [],
            [],
            [],
            [],
            []
        );

        $this->expectException(InvalidRequestArgumentException::class);

        $result = $this->resolver->resolve($request, $argument);
        $result->current();
    }
}
