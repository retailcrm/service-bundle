<?php

namespace RetailCrm\ServiceBundle\Messenger\Middleware;

interface LockableMessage
{
    public function __serialize(): array;
}
