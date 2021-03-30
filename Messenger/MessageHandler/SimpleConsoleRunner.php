<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use Psr\Log\LoggerInterface;
use RetailCrm\ServiceBundle\Messenger\CommandMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SimpleConsoleRunner
 *
 * @package RetailCrm\ServiceBundle\Messenger\MessageHandler
 */
class SimpleConsoleRunner implements JobRunner
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * CommandQueueHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     */
    public function __construct(LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
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
