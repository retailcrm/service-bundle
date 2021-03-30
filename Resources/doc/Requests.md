### Deserialize incoming requests

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
