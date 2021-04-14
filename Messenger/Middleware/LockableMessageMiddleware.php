<?php

namespace RetailCrm\ServiceBundle\Messenger\Middleware;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Throwable;

/**
 * Class LockableMessageMiddleware
 *
 * @package RetailCrm\ServiceBundle\Messenger\Middleware
 */
class LockableMessageMiddleware implements MiddlewareInterface
{
    /**
     * @var LockFactory
     */
    private $lockFactory;

    public function __construct(LockFactory $lockFactory)
    {
        $this->lockFactory = $lockFactory;
    }

    /**
     * @param Envelope $envelope
     * @param StackInterface $stack
     *
     * @return Envelope
     *
     * @throws Throwable
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($envelope->all(ReceivedStamp::class) && $message instanceof LockableMessage) {
            $lock = $this->lockFactory->createLock($this->hash($message), null);
            if (!$lock->acquire()) {
                return $envelope;
            }

            try {
                return $stack->next()->handle($envelope, $stack);
            } catch (Throwable $exception) {
                throw $exception;
            } finally {
                $lock->release();
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }

    /**
     * @param LockableMessage $message
     *
     * @return string
     */
    private function hash(LockableMessage $message): string
    {
        return hash('crc32c', serialize($message));
    }
}
