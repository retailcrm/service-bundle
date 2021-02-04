<?php

namespace RetailCrm\ServiceBundle\Security;

use Symfony\Component\HttpFoundation\Request;

class CallbackClientAuthenticator extends AbstractClientAuthenticator
{
    public function supports(Request $request): bool
    {
        return $request->request->has(static::AUTH_FIELD) || $request->query->has(static::AUTH_FIELD);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
