<?php

namespace RetailCrm\ServiceBundle\Tests\Messenger;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Tests\DataFixtures\TestMessage;

class CommandMessageTest extends TestCase
{
    public function testMessage(): void
    {
        $message = new TestMessage();

        static::assertEquals('test', $message->getCommandName());
        static::assertEquals(['argument' => 'argument'], $message->getArguments());
        static::assertEquals(['option' => 'option'], $message->getOptions());
        static::assertEquals(['--option' => 'option'], $message->getFormattedOptions());

        $message->addOption('option2', 'option2');
        $message->addArgument('argument2', 'argument2');

        static::assertEquals(['argument' => 'argument', 'argument2' => 'argument2'], $message->getArguments());
        static::assertEquals(['option' => 'option', 'option2' => 'option2'], $message->getOptions());
        static::assertEquals(['--option' => 'option', '--option2' => 'option2'], $message->getFormattedOptions());

        $message->setOptions(['option' => 'option']);
        $message->setArguments(['argument' => 'argument']);

        static::assertEquals(['argument' => 'argument'], $message->getArguments());
        static::assertEquals(['option' => 'option'], $message->getOptions());
        static::assertEquals(['--option' => 'option'], $message->getFormattedOptions());
    }
}
