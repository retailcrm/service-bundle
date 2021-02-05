<?php

namespace RetailCrm\ServiceBundle\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class CallbackClientAuthenticator
 *
 * @package RetailCrm\ServiceBundle\Security
 */
class CallbackClientAuthenticator extends AbstractClientAuthenticator
{
    /**
     * {@inheritdoc }
     */
    public function supports(Request $request): bool
    {
        return $request->request->has(static::AUTH_FIELD) || $request->query->has(static::AUTH_FIELD);
    }

    /**
     * {@inheritdoc }
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
