<?php

namespace RetailCrm\ServiceBundle\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Class InvalidRequestArgumentException
 *
 * @package RetailCrm\ServiceBundle\Exceptions
 */
class InvalidRequestArgumentException extends InvalidArgumentException
{
    private $validateErrors;

    /**
     * InvalidRequestArgumentException constructor.
     * @param string         $message
     * @param int            $code
     * @param array          $errors
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, iterable $errors = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->validateErrors = $errors;
    }

    /**
     * @return iterable
     */
    public function getValidateErrors(): iterable
    {
        return $this->validateErrors;
    }
}
