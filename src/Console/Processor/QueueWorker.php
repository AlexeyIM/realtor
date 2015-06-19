<?php

namespace Realtor\Console\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Parser\Factory as ParserFactory;
use Realtor\Advert\Link;
use Apix\Log\Logger;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class QueueWorker
 * @package Realtor\Console\Processor
 */
class QueueWorker extends AbstractProcessor
{
    /**
     * @var OutputInterface
     */
    private $output;

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
        $this->output = $output;
        $this->queue->listen(array($this, 'parseAdvertPage'));
    }

    /**
     * @param AMQPMessage $message
     */
    public function parseAdvertPage(AMQPMessage $message)
    {
        /** @var Link $link */
        $link = unserialize($message->body);
        $this->output->writeln('parsing ' . $link->getUrl());
        $logsPath = $this->getConfig('logs');

        $errorLog = new Logger\File($logsPath['alerts']);
        $resultLog = new Logger\File($logsPath['result']);

        $advertSource = $link->getSource();
        $advertParser = ParserFactory::createAdvertParser($advertSource);
        $advertParser->setRules($this->getConfig('rules'));

        try {
            if ($advert = $advertParser->parsePage($link->getUrl())) {
                $resultLog->info('<a href="' . $link->getUrl() . '">' . $advert->getTitle() . '</a><br>');
            }
            $line = sprintf('<info>passed</info>', $link->getUrl());
            $this->output->writeln($line);
        } catch (\Exception $e) {
            $line = sprintf('<error>%s</error>', $e->getMessage());
            $this->output->writeln($line);
            $errorLog->info(strip_tags($line));
        }
    }
}
