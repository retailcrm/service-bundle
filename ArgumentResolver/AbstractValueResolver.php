<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use RetailCrm\ServiceBundle\Exceptions\InvalidRequestArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValueResolver
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $data
     */
    protected function validate(object $data): void
    {
        $errors = $this->validator->validate($data);
        if (0 !== count($errors)) {
            throw new InvalidRequestArgumentException(
                sprintf("Invalid request parameter %s", \get_class($data)),
                400,
                $errors
            );
        }
    }
}
