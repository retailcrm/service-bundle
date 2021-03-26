<?php

namespace RetailCrm\ServiceBundle\Messenger;

use RetailCrm\ServiceBundle\Messenger\MessageHandler\JobRunner;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Exception;

/**
 * Class MessageHandler
 *
 * @package RetailCrm\ServiceBundle\Messenger
 */
class MessageHandler implements MessageHandlerInterface
{
    /**
     * @var JobRunner
     */
    private $runner;

    /**
     * CommandQueueHandler constructor.
     *
     * @param JobRunner $runner
     */
    public function __construct(JobRunner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param Message $message
     *
     * @throws Exception
     */
    public function __invoke(Message $message): void
    {
        $this->runner->run($message);
    }
}
