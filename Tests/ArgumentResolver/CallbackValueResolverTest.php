<?php

namespace RetailCrm\ServiceBundle\Tests\ArgumentResolver;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use RetailCrm\ServiceBundle\Exceptions\InvalidRequestArgumentException;
use RetailCrm\ServiceBundle\Tests\DataFixtures\RequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Generator;

class CallbackValueResolverTest extends TestCase
{
    private $resolver;

    public function setUp(): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->resolver = new CallbackValueResolver(
            $serializer,
            Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->getValidator(),
            [
                [
                    'type' => RequestDto::class,
                    'params' => ['request_parameter']
                ]
            ]
        );
    }

    public function testSupports(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            [],
            ['request_parameter' => json_encode(['param' => 'parameter'], JSON_THROW_ON_ERROR)],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST]
        );

        $result = $this->resolver->supports($request, $argument);

        static::assertTrue($result);
    }

    public function testNotSupports(): void
    {
        $argument = new ArgumentMetadata('RequestDto', 'NotFoundRequestDto', false, false, null);
        $request = new Request(
            [],
            ['request_parameter' => json_encode(['param' => 'parameter'], JSON_THROW_ON_ERROR)],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST]
        );

        $result = $this->resolver->supports($request, $argument);

        static::assertFalse($result);
    }

    public function testResolve(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            [],
            ['request_parameter' => json_encode(['param' => 'parameter'], JSON_THROW_ON_ERROR)],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST]
        );

        $result = $this->resolver->resolve($request, $argument);

        static::assertInstanceOf(Generator::class, $result);
        static::assertInstanceOf(RequestDto::class, $result->current());
        static::assertEquals('parameter', $result->current()->param);
    }

    public function testResolveFailure(): void
    {
        $argument = new ArgumentMetadata('RequestDto', RequestDto::class, false, false, null);
        $request = new Request(
            [],
            ['request_parameter' => json_encode(['param' => null], JSON_THROW_ON_ERROR)],
            [],
            [],
            [],
            ['REQUEST_METHOD' => Request::METHOD_POST]
        );

        $this->expectException(InvalidRequestArgumentException::class);

        $result = $this->resolver->resolve($request, $argument);
        $result->current();
    }
}
