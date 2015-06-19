<?php

namespace Realtor\Module\Onliner\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Advert\Link;
use Realtor\Module\Onliner\Parser\Listing;
use Realtor\Console\Processor\AbstractProcessor;

/**
 * Class LinkCollector
 * @package Module\Onliner\Processor
 */
class LinkCollector extends AbstractProcessor
{
    /**
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function process(OutputInterface $output)
    {
        $config = $this->getConfig('modules');

        if (!isset($config['onliner']['listing_mask'])) {
            throw new \Exception('Onliner module config was not found');
        }

        $output->writeln('Looking for links');
        $listingParser = new Listing();
        $links = $listingParser->parsePage($config['onliner']['listing_mask']);

        $output->writeln('Adding links to queue');
        foreach ($links as $link) {
            $link = new Link($link, Link::SOURCE_ONLINER);
            $this->queue->enqueue($link);
        }

        return count($links);
    }
}
