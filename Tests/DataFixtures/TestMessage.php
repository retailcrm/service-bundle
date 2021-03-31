<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;

class TestMessage extends CommandMessage
{
    public function __construct()
    {
        $this->commandName = 'test';
        $this->arguments = ['argument' => 'argument'];
        $this->options = ['option' => 'option'];
    }
}
