<?php

namespace RetailCrm\ServiceBundle\Security;

use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Class FrontApiClientAuthenticator
 *
 * @package RetailCrm\ServiceBundle\Security
 */
class FrontApiClientAuthenticator extends AbstractClientAuthenticator
{
    private $security;

    /**
     * FrontApiClientAuthenticator constructor.
     *
     * @param ErrorJsonResponseFactory $errorResponseFactory
     * @param Security                 $security
     */
    public function __construct(
        ErrorJsonResponseFactory $errorResponseFactory,
        Security $security
    ) {
        parent::__construct($errorResponseFactory);

        $this->security = $security;
    }

    /**
     * {@inheritdoc }
     */
    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        return $request->request->has(static::AUTH_FIELD);
    }

    /**
     * {@inheritdoc }
     */
    public function supportsRememberMe(): bool
    {
        return true;
    }
}
