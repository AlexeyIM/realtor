<?php
namespace Realtor\Console\Command;

use Realtor\Console\Processor\ProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    const NOTION_PROCESSOR_STARTED = '*** Starting %s processor';
    const NOTION_PROCESSOR_FINISHED = '*** %s finished';

    /**
     * @var array
     */
    protected $processors = array();

    /**
     * Process setter
     *
     * @param ProcessorInterface $processor
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * Starts business processes
     *
     * @param OutputInterface $output
     */
    protected function process(OutputInterface $output)
    {
        /** @var ProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            $className = get_class($processor);

            $output->writeln(sprintf(self::NOTION_PROCESSOR_STARTED, $className));
            $processor->process($output);
            $output->writeln(sprintf(self::NOTION_PROCESSOR_FINISHED, $className));
        }
    }
}