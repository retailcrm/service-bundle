### Authentication

Example security configuration:

```yaml
security:
    providers:
        client:
            entity:
                class: 'App\Entity\Connection' # must implements UserInterface
                property: 'clientId'
    firewalls:
        api:
            pattern: ^/api
            provider: client
            anonymous: ~
            lazy: true
            stateless: false
            guard:
                authenticators:
                    - RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator
        callback:
            pattern: ^/callback
            provider: client
            anonymous: ~
            lazy: true
            stateless: true
            guard:
                authenticators:
                    - RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator
        main:
            anonymous: true
            lazy: true

    access_control:
         - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY } # login for programmatically authentication user
         - { path: ^/api, roles: ROLE_USER }
         - { path: ^/callback, roles: ROLE_USER }
```

To authenticate the user after creating it, you can use the following code

```php

use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    public function someAction(
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        FrontApiClientAuthenticator $frontApiClientAuthenticator,
        ConnectionManager $manager
    ): Response {
        $user = $manager->getUser(); // getting user

        $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $frontApiClientAuthenticator,
            'api'
        );
        // ...
    }
}

```
