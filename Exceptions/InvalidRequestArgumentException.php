<?php

namespace RetailCrm\ServiceBundle\Exceptions;

use InvalidArgumentException;
use Throwable;

class InvalidRequestArgumentException extends InvalidArgumentException
{
    private $validateErrors;

    public function __construct(string $message = "", int $code = 0, array $errors = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->validateErrors = $errors;
    }

    public function getValidateErrors(): array
    {
        return $this->validateErrors;
    }
}
