<?php

namespace Realtor\Console\Processor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IProcessor
 * @package Realtor\Processor
 */
interface ProcessorInterface
{
    /**
     * @param OutputInterface $output
     * @return mixed
     */
    public function process(OutputInterface $output);
}
