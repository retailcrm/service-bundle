<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;

interface JobRunner
{
    public function run(CommandMessage $message): void;
}
