<?php

namespace Realtor\Module\RealtBy\Processor;

use Realtor\Console\Processor\IProcessor;
use Noodlehaus\Config;

/**
 * Class AbstractProcessor
 * @package Realtor\Module\RealtBy\Processor
 */
abstract class AbstractProcessor implements IProcessor
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * Config setter
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Config getter
     *
     * @param string $key
     * @param mixed $default
     * @return Config|mixed
     */
    protected function _getConfig($key = '', $default = null)
    {
        return $key ? $this->_config->get($key, $default) : $this->_config;
    }
}