<?php

namespace Realtor\Module\RealtBy\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Module\RealtBy\Parser\Advert;
use Realtor\Queue\Storage as Queue;
use Apix\Log\Logger;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class AdvertCollector
 * @package Realtor\Module\RealtBy\Processor
 */
class AdvertCollector extends AbstractProcessor
{
    /**
     * @var OutputInterface
     */
    private $_output;

    /**
     * Processing function
     *
     * @param OutputInterface $output
     * @throws \Exception
     *
     * @return int
     */
    public function process(OutputInterface $output)
    {
        $this->_output = $output;

        $queue = new Queue($this->_getConfig('queue_key'));
        $queue->dequeue(array($this, 'parseAdvertPage'));
    }

    /**
     * @param AMQPMessage $link
     */
    public function parseAdvertPage(AMQPMessage $link)
    {
        $this->_output->writeln('parsing ' . $link->body);
        $logsPath = $this->_getConfig('logs');

        $errorLog = new Logger\File($logsPath['alerts']);
        $resultLog = new Logger\File($logsPath['result']);

        $advertParser = new Advert();
        $advertParser->setRules($this->_getConfig('rules'));

        try {
            if ($title = $advertParser->parsePage($link->body)) {
                $resultLog->info('<a href="' . $link->body . '">' . $title . '</a><br>');
            }
            $message = sprintf('<info>passed</info>', $link->body);
            $this->_output->writeln($message);
        } catch (\Exception $e) {
            $message = sprintf('<error>%s</error>', $e->getMessage());
            $this->_output->writeln($message);
            $errorLog->info(strip_tags($message));
        }
    }
}