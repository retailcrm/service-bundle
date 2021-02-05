<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Generator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CallbackValueResolver
 *
 * @package RetailCrm\ServiceBundle\ArgumentResolver
 */
class CallbackValueResolver extends AbstractValueResolver implements ArgumentValueResolverInterface
{
    private $serializer;
    private $requestSchema;

    /**
     * CallbackValueResolver constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validator
     * @param array               $requestSchema
     */
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

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return string|null
     */
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
