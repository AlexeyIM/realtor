<?php

namespace Realtor\Console\Processor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IProcessor
 * @package Realtor\Processor
 */
interface IProcessor
{
    /**
     * @param OutputInterface $output
     * @return mixed
     */
    public function process(OutputInterface $output);
}