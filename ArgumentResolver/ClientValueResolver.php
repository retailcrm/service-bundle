<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use RetailCrm\ServiceBundle\Serializer\Adapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Generator;

/**
 * Class ClientValueResolver
 *
 * @package RetailCrm\ServiceBundle\ArgumentResolver
 */
class ClientValueResolver extends AbstractValueResolver implements ArgumentValueResolverInterface
{
    private $serializer;
    private $requestSchema;

    /**
     * ClientValueResolver constructor.
     *
     *
     * @param Adapter               $serializer
     * @param ValidatorInterface    $validator
     * @param array                 $requestSchema
     */
    public function __construct(
        Adapter $serializer,
        ValidatorInterface $validator,
        array $requestSchema
    ) {
        parent::__construct($validator);

        $this->serializer = $serializer;
        $this->requestSchema = $requestSchema;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return in_array($argument->getType(), $this->requestSchema, true);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (Request::METHOD_GET === $request->getMethod()) {
            $dto = $this->handleGetData($request->query->all(), $argument->getType());
        } else {
            $dto = $this->handlePostData($request->getContent(), $argument->getType());
        }

        $this->validate($dto);

        yield $dto;
    }

    /**
     * @param array  $data
     * @param string $type
     *
     * @return object
     */
    private function handleGetData(array $data, string $type): object
    {
        return $this->serializer->arrayToObject($data, $type);
    }

    /**
     * @param string $data
     * @param string $type
     *
     * @return object
     */
    private function handlePostData(string $data, string $type): object
    {
        return $this->serializer->deserialize($data, $type);
    }
}
