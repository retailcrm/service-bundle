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
        callback:
            pattern: ^/callback
            provider: connection
            stateless: true
            custom_authenticators:
                - RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator
        front:
            pattern: ^/auth
            provider: connection
            stateless: false
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800 # 1 week in seconds
                signature_properties: ['clientId']
                always_remember_me: true
            custom_authenticators:
                - RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator
        main:
            pattern: ^/
            lazy: true

    access_control:
        - { path: ^/front, roles: IS_AUTHENTICATED_REMEMBERED }
        - { path: ^/simple-connection, roles: PUBLIC_ACCESS }
```

Login controller will be called after the authenticator successfully authenticates the user. You can get the authenticated user, generate a token (or whatever you need to return) and return response:

```php

    use App\Entity\User;
    use Symfony\Component\Security\Http\Attribute\CurrentUser;

    class ApiLoginController extends AbstractController
    {
        #[Route('/auth', name: 'auth')]
        public function auth(#[CurrentUser] ?User $user): Response
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
