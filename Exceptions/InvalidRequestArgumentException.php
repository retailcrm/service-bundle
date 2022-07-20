<?php

namespace RetailCrm\ServiceBundle\Exceptions;

use InvalidArgumentException;
use Throwable;

class InvalidRequestArgumentException extends InvalidArgumentException
{
    private $validateErrors;

    public function __construct(string $message = "", int $code = 0, iterable $errors = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->validateErrors = $errors;
    }

    public function getValidateErrors(): iterable
    {
        return $this->validateErrors;
    }
}
