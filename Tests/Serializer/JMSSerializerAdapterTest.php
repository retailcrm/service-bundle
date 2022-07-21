<?php

namespace RetailCrm\ServiceBundle\Tests\Serializer;

use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Serializer\JMSSerializerAdapter;
use RetailCrm\ServiceBundle\Tests\DataFixtures\RequestDto;

class JMSSerializerAdapterTest extends TestCase
{
    private $serializer;
    private $transformer;

    protected function setUp(): void
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->transformer = SerializerBuilder::create()->build();
    }

    public function testDeserialize(): void
    {
        $adapter = new JMSSerializerAdapter($this->serializer, $this->transformer);
        $object = $adapter->deserialize('{"param": "string"}', RequestDto::class,'json');

        static::assertInstanceOf(RequestDto::class, $object);
        static::assertEquals('string', $object->param);
    }

    public function testArrayToObject(): void
    {
        $adapter = new JMSSerializerAdapter($this->serializer, $this->transformer);
        $object = $adapter->arrayToObject(['param' => 'string'], RequestDto::class);

        static::assertInstanceOf(RequestDto::class, $object);
        static::assertEquals('string', $object->param);
    }
}
