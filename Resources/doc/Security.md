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
            pattern: ^/(front|login)
            provider: connection
            stateless: false
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800 # 1 week in seconds
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

To authenticate the user after creating it, you can use the following code

```php

    use App\Entity\Connection;
    use App\Services\ConnectionManager;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
    use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;

    class AppController extends AbstractController
    {
        public function someAction(
            Request $request,
            Connection $connection,
            ConnectionManager $manager,
            UserAuthenticatorInterface $userAuthenticator,
            FrontApiClientAuthenticator $authenticator
        ): Response {
            $exist = $manager->search($connection); //get connection

            $userAuthenticator->authenticateUser(
                $connection,
                $authenticator,
                $request
            );
        }
    }

```
