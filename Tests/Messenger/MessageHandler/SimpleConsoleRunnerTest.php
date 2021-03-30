<?php

namespace RetailCrm\ServiceBundle\Tests\Messenger\MessageHandler;

use Psr\Log\NullLogger;
use RetailCrm\ServiceBundle\Messenger\MessageHandler\SimpleConsoleRunner;
use RetailCrm\ServiceBundle\Tests\Fixtures\App\TestCommandMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimpleConsoleRunnerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
    }

    public function testRun(): void
    {
        $runner = new SimpleConsoleRunner(new NullLogger, self::$kernel);

        ob_clean();
        ob_start();
        $runner->run(new TestCommandMessage());

        static::assertEquals('test test test', ob_get_clean());
    }
}
