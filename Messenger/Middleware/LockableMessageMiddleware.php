<?php

namespace RetailCrm\ServiceBundle\Messenger\Middleware;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Throwable;

class LockableMessageMiddleware implements MiddlewareInterface
{
    public function __construct(private LockFactory $lockFactory)
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($envelope->all(ReceivedStamp::class) && $message instanceof LockableMessage) {
            $lock = $this->lockFactory->createLock($this->objectHash($message), null);
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

    private function objectHash(LockableMessage $message): string
    {
        return hash('crc32', serialize($message));
    }
}
