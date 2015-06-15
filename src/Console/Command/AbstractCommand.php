<?php
namespace Realtor\Console\Command;

use Realtor\Console\Processor\IProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    const NOTION_PROCESSOR_STARTED = '*** Starting %s processor';
    const NOTION_PROCESSOR_FINISHED = '*** %s finished';

    /**
     * @var array
     */
    protected $_processors = array();

    /**
     * Process setter
     *
     * @param IProcessor $processor
     */
    public function addProcessor(IProcessor $processor)
    {
        $this->_processors[] = $processor;
    }

    /**
     * Starts business processes
     *
     * @param OutputInterface $output
     */
    protected function process(OutputInterface $output)
    {
        /** @var IProcessor $processor */
        foreach ($this->_processors as $processor) {
            $className = get_class($processor);

            $output->writeln(sprintf(self::NOTION_PROCESSOR_STARTED, $className));
            $processor->process($output);
            $output->writeln(sprintf(self::NOTION_PROCESSOR_FINISHED, $className));
        }
    }
}