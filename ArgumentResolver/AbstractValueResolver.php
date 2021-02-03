<?php

namespace RetailCrm\ServiceBundle\ArgumentResolver;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use InvalidArgumentException;

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
            throw new InvalidArgumentException($errors);
        }
    }
}
