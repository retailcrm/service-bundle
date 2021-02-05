<?php

namespace RetailCrm\ServiceBundle\Tests\Response;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Models\Error;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ErrorJsonResponseFactoryTest extends TestCase
{
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function testCreate(): void
    {
        $factory = new ErrorJsonResponseFactory($this->serializer);
        $error = new Error();
        $error->message = 'Test error message';

        $result = $factory->create($error);

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        static::assertEquals('{"code":null,"message":"Test error message","details":null}', $result->getContent());
    }
}
