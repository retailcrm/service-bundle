<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use RetailCrm\ServiceBundle\Serializer\Adapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Generator;

class ClientValueResolver extends AbstractValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        ValidatorInterface $validator,
        private Adapter $serializer,
        private array $requestSchema
    ) {
        parent::__construct($validator);
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return in_array($argument->getType(), $this->requestSchema, true);
    }

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

    private function handleGetData(array $data, string $type): object
    {
        return $this->serializer->arrayToObject($data, $type);
    }

    private function handlePostData(string $data, string $type): object
    {
        return $this->serializer->deserialize($data, $type);
    }
}
