<?php

namespace RetailCrm\ServiceBundle\Messenger;

use RetailCrm\ServiceBundle\Messenger\MessageHandler\JobRunner;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Exception;

class MessageHandler implements MessageHandlerInterface
{
    public function __construct(private JobRunner $runner)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(CommandMessage $message): void
    {
        $this->runner->run($message);
    }
}
