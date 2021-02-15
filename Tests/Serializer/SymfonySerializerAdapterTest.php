<?php

namespace RetailCrm\ServiceBundle\Tests\Serializer;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Serializer\SymfonySerializerAdapter;
use RetailCrm\ServiceBundle\Tests\DataFixtures\RequestDto;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SymfonySerializerAdapterTest extends TestCase
{
    private $serializer;
    private $denormalizer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->denormalizer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function testDeserialize(): void
    {
        $adapter = new SymfonySerializerAdapter($this->serializer, $this->denormalizer);
        $object = $adapter->deserialize('{"param": "string"}', RequestDto::class,'json');

        static::assertInstanceOf(RequestDto::class, $object);
        static::assertEquals('string', $object->param);
    }

    public function testArrayToObject(): void
    {
        $adapter = new SymfonySerializerAdapter($this->serializer, $this->denormalizer);
        $object = $adapter->arrayToObject(['param' => 'string'], RequestDto::class);

        static::assertInstanceOf(RequestDto::class, $object);
        static::assertEquals('string', $object->param);
    }
}
