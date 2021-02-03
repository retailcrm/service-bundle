<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Generator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CallbackValueResolver extends AbstractValueResolver implements ArgumentValueResolverInterface
{
    private $serializer;
    private $requestSchema;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        array $requestSchema
    ) {
        parent::__construct($validator);

        $this->serializer = $serializer;
        $this->requestSchema = $requestSchema;
    }

    /**
     * {@inheritdoc }
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (empty($this->requestSchema) || $request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        return null !== $this->search($request, $argument);
    }

    /**
     * {@inheritdoc }
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $parameter = $this->search($request, $argument);
        $data = $this->serializer->deserialize($request->request->get($parameter), $argument->getType(), 'json');
        $this->validate($data);

        yield $data;
    }

    private function search(Request $request, ArgumentMetadata $argument): ?string
    {
        foreach ($this->requestSchema as $callback) {
            if (!$argument->getName() === $callback['type']) {
                continue;
            }

            foreach ($callback['params'] as $param) {
                if ($request->request->has($param)) {
                    return $param;
                }
            }
        }

        return null;
    }
}
