<?php

namespace RetailCrm\ServiceBundle\Response;

use RetailCrm\ServiceBundle\Models\Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ErrorJsonResponseFactory
 *
 * @package RetailCrm\ServiceBundle\Response
 */
class ErrorJsonResponseFactory
{
    private $serializer;

    /**
     * ErrorJsonResponseFactory constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Error $error
     * @param int   $statusCode
     * @param array $headers
     *
     * @return Response
     */
    public function create(Error $error, int $statusCode = Response::HTTP_BAD_REQUEST, array $headers = []): Response
    {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($error, 'json'),
            $statusCode,
            $headers
        );
    }
}
