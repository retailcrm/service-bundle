<?php

namespace RetailCrm\ServiceBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SymfonySerializerAdapter implements Adapter
{
    private array $context = [];

    public function __construct(private SerializerInterface $serializer, private DenormalizerInterface $denormalizer)
    {
    }

    public function deserialize(string $data, string $type,string $format = 'json'): object
    {
        return $this->serializer->deserialize($data, $type, $format, $this->context);
    }

    public function arrayToObject(array $data, string $type, string $format = null): object
    {
        return $this->denormalizer->denormalize($data, $type, $format, $this->context);
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}
