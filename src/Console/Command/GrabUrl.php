<?php

namespace Realtor\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GrabUrl
 * @package Realtor\Console\Command
 */
class GrabUrl extends AbstractCommand
{
    /**
     * Command config definition
     */
    protected function configure()
    {
        $this
            ->setName('realtor:graburl')
            ->setDescription('Collects advert urls from sites');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Collecting links...', OutputInterface::OUTPUT_NORMAL);

        $this->process($output);

        /** @var \Symfony\Component\Console\Helper\FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');
        $formattedLine = $formatter->formatSection(
            'Finished',
            sprintf('%d items were processed', 0)
        );

        $output->writeln($formattedLine);
    }
}