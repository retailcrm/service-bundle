<?php

namespace RetailCrm\ServiceBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SymfonySerializerAdapter
 *
 * @package RetailCrm\ServiceBundle\Serializer
 */
class SymfonySerializerAdapter implements Adapter
{
    private $serializer;
    private $denormalizer;
    private $context = [];

    /**
     * SymfonySerializerAdapter constructor.
     *
     * @param SerializerInterface   $serializer
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(SerializerInterface $serializer, DenormalizerInterface $denormalizer)
    {
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc }
     */
    public function deserialize(string $data, string $type,string $format = 'json'): object
    {
        return $this->serializer->deserialize($data, $type, $format, $this->context);
    }

    /**
     * {@inheritdoc }
     */
    public function arrayToObject(array $data, string $type, string $format = null): object
    {
        return $this->denormalizer->denormalize($data, $type, $format, $this->context);
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}
