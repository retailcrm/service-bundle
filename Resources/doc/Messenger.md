### Messenger

#### Messages

The library provides a basic message for executing console commands as message handlers - `RetailCrm\ServiceBundle\Messenger\CommandMessage`.
This makes it easier to create new message types. For example:

* Create your message

```php

namespace App\Message;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;

class MyMessage extends CommandMessage
{
    public function __construct()
    {
        $this->commandName = \App\Command\MyCommand::getDefaultName();
        $this->options = ['optionName' => 'optionValue'];
        $this->arguments = ['argumentName' => 'argumentValue'];
    }
}

```

* Add a message to a routing

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: "%env(MESSENGER_TRANSPORT_DSN)%"

        routing:
            'App\Message\MyMessage': async
```

Now when sending this message through messenger (```$messageBus->dispatch(new MyMessage())```) the message handler will run the associated command

#### Message handlers

Two messages handlers are is supported:

* all messages handling in one worker process
* each message is a processed in a separate process

By default, messages will be processed in one process. To set up a handler to run in a separate process, add to the bundle config:

```yaml
retail_crm_service:
    messenger:
        message_handler: in_new_process_runner
        process_timeout: 60
```

`process_timeout` - an optional parameter that only makes sense when `message_handler` is equal `in_new_process_runner` and determines the lifetime of the process.
By default, process timeout - 3600 (in seconds).
