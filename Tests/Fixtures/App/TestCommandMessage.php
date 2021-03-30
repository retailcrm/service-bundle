<?php

namespace RetailCrm\ServiceBundle\Tests\Fixtures\App;

use RetailCrm\ServiceBundle\Messenger\CommandMessage;

class TestCommandMessage extends CommandMessage
{
    public function __construct()
    {
        $this->commandName = 'test';
        $this->arguments = ['argument' => 'test'];
        $this->options = ['option' => 'test'];
    }
}
