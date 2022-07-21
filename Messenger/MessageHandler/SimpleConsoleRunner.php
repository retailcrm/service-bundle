<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use Psr\Log\LoggerInterface;
use RetailCrm\ServiceBundle\Messenger\CommandMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class SimpleConsoleRunner implements JobRunner
{
    public function __construct(private LoggerInterface $logger, private KernelInterface $kernel)
    {
    }

    public function run(CommandMessage $message): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array_merge(
                ['command' => $message->getCommandName()],
                $message->getFormattedOptions(),
                $message->getArguments()
            )
        );

        $output = new BufferedOutput();
        if ($application->run($input, $output) > 0) {
            $this->logger->error($output->fetch());

            return;
        }

        echo $output->fetch();
    }
}
