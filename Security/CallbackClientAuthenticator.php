<?php

namespace RetailCrm\ServiceBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

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

    /**
     * {@inheritdoc }
     */
    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get(static::AUTH_FIELD);

        return new SelfValidatingPassport(
            new UserBadge($identifier, function ($userIdentifier) {
                return $this->repository->findByIdentifier($userIdentifier);
            }),
            []
        );
    }
}
