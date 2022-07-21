<?php

namespace RetailCrm\ServiceBundle\Tests\Messenger;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Messenger\CommandMessage;
use RetailCrm\ServiceBundle\Messenger\MessageHandler;
use RetailCrm\ServiceBundle\Messenger\MessageHandler\JobRunner;

class MessageHandlerTest extends TestCase
{
    public function testRun(): void
    {
        $runner = $this->createMock(JobRunner::class);
        $runner->expects(static::once())->method('run');
        $message = $this->createMock(CommandMessage::class);

        $handler = new MessageHandler($runner);
        $handler->__invoke($message);
    }
}
