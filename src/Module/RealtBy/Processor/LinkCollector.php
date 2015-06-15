<?php

namespace Realtor\Module\RealtBy\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Module\RealtBy\Parser\Breadcrumbs;
use Realtor\Module\RealtBy\Parser\Listing;
use Realtor\Queue\Storage as Queue;

/**
 * Class LinkCollector
 * @package Realtor\Module\RealtBy\Processor
 */
class LinkCollector extends AbstractProcessor
{
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
        $listingUrl = preg_replace('/(\%\d)/', '%$1', $this->_getConfig('listing_mask'));

        $breadcrumbParser = new Breadcrumbs();
        $pageCount = $breadcrumbParser->parsePage(sprintf($listingUrl, 1));
        $output->writeln('Pages found: ' . $pageCount);

        $links = [];
        $listingParser = new Listing();
        for ($i = 1; $i <= $pageCount; $i++) {
            $parsedLinks = $listingParser->parsePage(sprintf($listingUrl, $i));
            $links = array_merge($links, $parsedLinks);
            $output->writeln('Links found for page ' . $i . ' found: ' . count($parsedLinks));
        }

        $output->writeln('Adding links to queue');
        $queue_id = $this->_getConfig('queue_key');
        $queue = new Queue($queue_id);
        foreach ($links as $link) {
            $queue->enqueue($link);
        }

        return count($links);
    }
}