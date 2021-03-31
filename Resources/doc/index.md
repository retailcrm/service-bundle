## Installation

`composer require retailcrm/service-bundle`

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
    messenger: ~
```

## Usage
* [Handling incoming requests data](./Requests.md)
* [Security](./Security.md)
* [Messenger](./Messenger.md)
