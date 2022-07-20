<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use RetailCrm\ServiceBundle\Serializer\Adapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Generator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CallbackValueResolver extends AbstractValueResolver implements ArgumentValueResolverInterface
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
        if (empty($this->requestSchema) || $request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        return null !== $this->search($request, $argument);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $parameter = $this->search($request, $argument);
        $data = $this->serializer->deserialize($request->request->get($parameter), $argument->getType());
        $this->validate($data);

        yield $data;
    }

    private function search(Request $request, ArgumentMetadata $argument): ?string
    {
        foreach ($this->requestSchema as $callback) {
            if ($argument->getType() !== $callback['type']) {
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
