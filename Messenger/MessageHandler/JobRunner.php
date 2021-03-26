<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use RetailCrm\ServiceBundle\Messenger\Message;

/**
 * Interface JobRunner
 *
 * @package RetailCrm\ServiceBundle\Messenger\MessageHandler
 */
interface JobRunner
{
    /**
     * @param Message $message
     */
    public function run(Message $message): void;
}
