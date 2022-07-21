### Authentication

Example security configuration:

```yaml
security:
    hide_user_not_found: false
    providers:
        connection:
            entity: { class: App\Entity\Connection, property: clientId }
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        simple-connection:
            pattern: ^/simple-connection
            stateless: true
            security: false
        front:
            pattern: ^/front
            provider: connection
            stateless: true
            custom_authenticators:
                - RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator
        main:
            pattern: ^/
            lazy: true

    access_control:
        - { path: ^/front, roles: IS_AUTHENTICATED_FULLY }
        - { path: ^/(simple-connection), roles: PUBLIC_ACCESS }
```

Login controller will be called after the authenticator successfully authenticates the user. You can get the authenticated user, generate a token (or whatever you need to return) and return response:

```php

    use App\Entity\User;
    use Symfony\Component\Security\Http\Attribute\CurrentUser;

    class ApiLoginController extends AbstractController
    {
        #[Route('/front', name: 'front')]
        public function front(#[CurrentUser] ?User $user): Response
        {
            $token = ...; // somehow create an API token for $user
 
            return $this->json([
                'user'  => $user->getUserIdentifier(),
                'token' => $token,
            ]);
        }
    }

```

The <code>#[CurrentUser]</code> can only be used in controller arguments to retrieve the authenticated user. In services, you would use getUser().

See the [manual](https://symfony.com/doc/6.0/security.html) for more information.

> If you set the parameter stateless: false, then during an active session the login will be made on the basis of the data deserialized from the session storage