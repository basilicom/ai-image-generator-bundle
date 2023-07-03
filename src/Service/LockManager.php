<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Symfony\Component\Lock\LockFactory;

class LockManager
{
    private const LOCK_KEY = 'ai-image-generator';

    private LockFactory $lockFactory;

    public function __construct(LockFactory $lockFactory)
    {
        $this->lockFactory = $lockFactory;
    }

    public function lock(): void
    {
        $lock = $this->lockFactory->createLock(self::LOCK_KEY);
        $lock->acquire(true);
    }

    public function unlock(): void
    {
        $lock = $this->lockFactory->createLock(self::LOCK_KEY);
        $lock->release();
    }

    public function isLocked(): bool
    {
        $lock = $this->lockFactory->createLock(self::LOCK_KEY);

        return !$lock->acquire();
    }
}
