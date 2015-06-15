<?php

namespace Realtor\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessQueue
 * @package Realtor\Console\Command
 */
class ProcessQueue extends AbstractCommand
{
    /**
     * Command config definition
     */
    protected function configure()
    {
        $this
            ->setName('realtor:processqueue')
            ->setDescription('Processing urls from queue');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Worker started', OutputInterface::OUTPUT_NORMAL);

        $this->process($output);
    }
}