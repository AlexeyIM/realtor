<?php

namespace Realtor\Console\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Parser\Factory as ParserFactory;
use Realtor\Advert\Link;
use Apix\Log\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use Realtor\Advert\Advert;

/**
 * Class QueueWorker
 * @package Realtor\Console\Processor
 */
class QueueWorker extends AbstractProcessor
{
    const ADVERT_RESULT_LINE = '<a href="%s">%s</a> %d$ за м<sup>2</sup><br>';

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

        $logsPath = $this->getConfig('logs');
        $this->writeResultHeader($logsPath['result']);

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
            /** @var Advert $advert */
            if ($advert = $advertParser->parsePage($link->getUrl())) {
                $result = sprintf(
                    self::ADVERT_RESULT_LINE,
                    $link->getUrl(),
                    $advert->getTitle(),
                    $advert->getPricePerMeter()
                );
                $resultLog->info($result);
            }
            $line = sprintf('<info>passed</info>', $link->getUrl());
            $this->output->writeln($line);
        } catch (\Exception $e) {
            $line = sprintf('<error>%s</error>', $e->getMessage());
            $this->output->writeln($line);
            $errorLog->info(strip_tags($line));
        }
    }

    /**
     * Writes first line to determine file encoding
     *
     * @param string $path
     */
    protected function writeResultHeader($path)
    {
        $file = fopen($path, 'w');
        fwrite($file, '<head><meta charset="UTF-8"></head>');
        fclose($file);
    }
}
