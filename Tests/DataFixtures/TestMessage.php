<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;
use RetailCrm\ServiceBundle\Messenger\Middleware\LockableMessage;

class TestMessage extends CommandMessage implements LockableMessage
{
    public function __construct()
    {
        $this->commandName = 'test';
        $this->arguments = ['argument' => 'argument'];
        $this->options = ['option' => 'option'];
    }
}
