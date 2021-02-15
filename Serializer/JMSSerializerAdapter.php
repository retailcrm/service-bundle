<?php

namespace RetailCrm\ServiceBundle\Serializer;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\SerializerInterface;

/**
 * Class JMSSerializerAdapter
 *
 * @package RetailCrm\ServiceBundle\Serializer
 */
class JMSSerializerAdapter implements Adapter
{
    private $serializer;
    private $transformer;
    private $context;

    /**
     * JMSSerializerAdapter constructor.
     *
     * @param SerializerInterface       $serializer
     * @param ArrayTransformerInterface $transformer
     */
    public function __construct(
        SerializerInterface $serializer,
        ArrayTransformerInterface $transformer
    ) {
        $this->serializer = $serializer;
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc }
     */
    public function deserialize(string $data, string $type, string $format = 'json'): object
    {
        return $this->serializer->deserialize($data, $type, $format, $this->context);
    }

    /**
     * {@inheritdoc }
     */
    public function arrayToObject(array $data, string $type, ?string $format = null): object
    {
        return $this->transformer->fromArray($data, $type, $this->context);
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}
