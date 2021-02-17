## Installation

`composer require retailcrm/service-bundle`

## Usage

Enable bundle in `config/bundles.php`:

```php
<?php

return [
    // other bundles
    RetailCrm\ServiceBundle\RetailCrmServiceBundle::class => ['all' => true]
];

```

Create bundle config file in `config/packages/retail_crm_service.yaml`:

```yaml
retail_crm_service:
    request_schema:
        callback: ~
        client: ~
```

### Deserializing incoming requests

#### Callbacks (form data)

To automatically get the callback request parameter

```php

class AppController extends AbstractController
{
    public function activityAction(\App\Dto\Callback\Activity $activity): Response
    {
        // handle activity
    }
}

```

add to the config:

```yaml
retail_crm_service:
      request_schema:
            callback:
                  supports:
                        - { type: App\Dto\Callback\Activity, params: ["activity"] }
```

request automatically will be deserialization to $activity.

#### Body json content

```php

class AppController extends AbstractController
{
    public function someAction(\App\Dto\Body $activity): Response
    {
        // handle activity
    }
}

```

add to the config:

```yaml
retail_crm_service:
    request_schema:
          client:
              supports:
                  - App\Dto\Body
```

#### Serializers
At this time supported [Symfony serializer](https://symfony.com/doc/current/components/serializer.html) and [JMS serializer](https://jmsyst.com/libs/serializer).
By default, the library using a Symfony serializer. For use JMS install JMS serializer bundle - `composer require jms/serializer-bundle`
You can explicitly specify the type of serializer used for request schema:

```yaml
retail_crm_service:
    request_schema:
          client:
              supports:
                  # types
              serializer: retail_crm_service.symfony_serializer.adapter # or retail_crm_service.jms_serializer.adapter
          callback:
              supports:
                  # types
              serializer: retail_crm_service.jms_serializer.adapter # or retail_crm_service.symfony_serializer.adapter
```

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
