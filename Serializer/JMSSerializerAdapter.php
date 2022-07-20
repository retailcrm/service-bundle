<?php

namespace RetailCrm\ServiceBundle\Serializer;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\SerializerInterface;

class JMSSerializerAdapter implements Adapter
{
    private $context;

    public function __construct(
        private SerializerInterface $serializer,
        private ArrayTransformerInterface $transformer
    ) {
    }

    public function deserialize(string $data, string $type, string $format = 'json'): object
    {
        return $this->serializer->deserialize($data, $type, $format, $this->context);
    }

    public function arrayToObject(array $data, string $type, ?string $format = null): object
    {
        return $this->transformer->fromArray($data, $type, $this->context);
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}
