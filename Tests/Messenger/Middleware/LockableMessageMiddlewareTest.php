<?php

namespace RetailCrm\ServiceBundle\Tests\Messenger\Middleware;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Messenger\Middleware\LockableMessageMiddleware;
use RetailCrm\ServiceBundle\Tests\DataFixtures\TestMessage;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

/**
 * Class LockableMessageMiddlewareTest
 *
 * @package RetailCrm\ServiceBundle\Tests\Messenger\Middleware
 */
class LockableMessageMiddlewareTest extends TestCase
{
    /**
     * @var LockFactory
     */
    private $lockFactory;

    protected function setUp(): void
    {
        $this->lockFactory = $this->createMock(LockFactory::class);
    }

    public function testHandle(): void
    {
        $store = $this->createMock(PersistingStoreInterface::class);
        $key = new Key(uniqid());
        $lock = new Lock($key, $store);
        $this->lockFactory->expects(static::once())->method('createLock')->willReturn($lock);
        $envelope = new Envelope(new TestMessage(), [new ReceivedStamp('test')]);

        $next = $this->createMock(MiddlewareInterface::class);
        $next->method('handle')->willReturn($envelope);
        $stack = $this->createMock(StackInterface::class);
        $stack->method('next')->willReturn($next);

        $middleware = new LockableMessageMiddleware($this->lockFactory);
        $result = $middleware->handle($envelope, $stack);

        static::assertInstanceOf(Envelope::class, $result);
    }

    public function testLockHandle(): void
    {
        $store = $this->createMock(PersistingStoreInterface::class);
        $store->method('save')->willThrowException(new LockConflictedException);
        $key = new Key(uniqid());
        $lock = new Lock($key, $store);
        $this->lockFactory->expects(static::once())->method('createLock')->willReturn($lock);
        $envelope = new Envelope(new TestMessage(), [new ReceivedStamp('test')]);

        $next = $this->createMock(MiddlewareInterface::class);
        $next->method('handle')->willReturn($envelope);
        $stack = $this->createMock(StackInterface::class);
        $stack->method('next')->willReturn($next);

        $middleware = new LockableMessageMiddleware($this->lockFactory);
        $result = $middleware->handle($envelope, $stack);

        static::assertInstanceOf(Envelope::class, $result);
    }

    public function testNonLockableHandle(): void
    {
        $store = $this->createMock(PersistingStoreInterface::class);
        $store->method('save')->willThrowException(new LockConflictedException);
        $key = new Key(uniqid());
        $lock = new Lock($key, $store);
        $this->lockFactory->expects(static::never())->method('createLock')->willReturn($lock);
        $envelope = new Envelope(new \stdClass(), [new ReceivedStamp('test')]);

        $next = $this->createMock(MiddlewareInterface::class);
        $next->method('handle')->willReturn($envelope);
        $stack = $this->createMock(StackInterface::class);
        $stack->method('next')->willReturn($next);

        $middleware = new LockableMessageMiddleware($this->lockFactory);
        $result = $middleware->handle($envelope, $stack);

        static::assertInstanceOf(Envelope::class, $result);
    }

    public function testNonReceivedHandle(): void
    {
        $store = $this->createMock(PersistingStoreInterface::class);
        $store->method('save')->willThrowException(new LockConflictedException);
        $key = new Key(uniqid());
        $lock = new Lock($key, $store);
        $this->lockFactory->expects(static::never())->method('createLock')->willReturn($lock);
        $envelope = new Envelope(new TestMessage());

        $next = $this->createMock(MiddlewareInterface::class);
        $next->method('handle')->willReturn($envelope);
        $stack = $this->createMock(StackInterface::class);
        $stack->method('next')->willReturn($next);

        $middleware = new LockableMessageMiddleware($this->lockFactory);
        $result = $middleware->handle($envelope, $stack);

        static::assertInstanceOf(Envelope::class, $result);
    }
}
