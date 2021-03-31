<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;

/**
 * Interface JobRunner
 *
 * @package RetailCrm\ServiceBundle\Messenger\MessageHandler
 */
interface JobRunner
{
    /**
     * @param CommandMessage $message
     */
    public function run(CommandMessage $message): void;
}
