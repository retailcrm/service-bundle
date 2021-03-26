<?php

namespace RetailCrm\ServiceBundle\Messenger\MessageHandler;

use Psr\Log\LoggerInterface;
use RetailCrm\ServiceBundle\Messenger\Message;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class InNewProcessRunner
 *
 * @package RetailCrm\ServiceBundle\Messenger\MessageHandler
 */
class InNewProcessRunner implements JobRunner
{
    /** @var int Default timeout for process */
    public const DEFAULT_TIMEOUT = 3600;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var int
     */
    private $timeout = self::DEFAULT_TIMEOUT;

    /**
     * CommandQueueHandler constructor.
     *
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     * @param int|null        $timeout
     */
    public function __construct(
        LoggerInterface $logger,
        KernelInterface $kernel,
        ?int $timeout = null
    ) {
        $this->logger = $logger;
        $this->kernel = $kernel;

        if (null !== $timeout) {
            $this->timeout = $timeout;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run(Message $message): void
    {
        $phpBinaryPath = (new PhpExecutableFinder)->find();
        $consoleCommand = [
            'php' => $phpBinaryPath ?: 'php',
            'console' => sprintf('%s/bin/console', $this->kernel->getContainer()->getParameter('kernel.project_dir')),
            'command' => $message->getCommandName()
        ];

        $process = new Process(
            array_merge(
                array_values($consoleCommand),
                array_values($message->getArguments()),
                array_values($this->getOptions($message)),
            )
        );

        try {
            $process
                ->setTimeout($this->timeout)
                ->run(static function(string $type, string $buffer) {
                    echo $buffer;
                })
            ;
        } catch (ProcessTimedOutException $processTimedOutException) {
            $this->logger->error(
                sprintf(
                    'Process "%s" killed after %d seconds of execution',
                    $processTimedOutException->getProcess()->getCommandLine(),
                    $processTimedOutException->getProcess()->getTimeout()
                )
            );
        }
    }

    /**
     * @param Message $message
     *
     * @return array
     */
    private function getOptions(Message $message): array
    {
        $options = [];
        foreach ($message->getFormattedOptions() as $option => $value) {
            $options[] = $option . '=' . $value;
        }

        return $options;
    }
}
