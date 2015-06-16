<?php

namespace Realtor\Queue;

/**
 * Class StorageInterface
 * @package Realtor\Queue
 */
interface StorageInterface
{
    public function enqueue($value);

    public function listen(callable $callback);
}
