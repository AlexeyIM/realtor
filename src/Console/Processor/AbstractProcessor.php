<?php

namespace Realtor\Console\Processor;

use Realtor\Queue\StorageInterface;
use Noodlehaus\Config;

/**
 * Class AbstractProcessor
 * @package Realtor\Module\RealtBy\Processor
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StorageInterface
     */
    protected $queue;

    /**
     * Initializing
     *
     * @param Config $config
     * @param StorageInterface $queue
     */
    public function __construct(Config $config, StorageInterface $queue)
    {
        $this->config = $config;
        $this->queue = $queue;
    }

    /**
     * Config getter
     *
     * @param string $key
     * @param mixed $default
     * @return Config|mixed
     */
    protected function getConfig($key = '', $default = null)
    {
        return $key ? $this->config->get($key, $default) : $this->config;
    }
}
