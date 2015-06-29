<?php

namespace Realtor\Module\RealtBy\Processor;

use Symfony\Component\Console\Output\OutputInterface;
use Realtor\Advert\Link;
use Realtor\Module\RealtBy\Parser\Breadcrumbs;
use Realtor\Module\RealtBy\Parser\Listing;
use Realtor\Console\Processor\AbstractProcessor;

/**
 * Class LinkCollector
 * @package Realtor\Module\RealtBy\Processor
 */
class LinkCollector extends AbstractProcessor
{
    const CANT_FIND_CONFIG = 'RealtBy module config was not found';

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
        $config = $this->getConfig('modules');

        if (!isset($config['realtby']['listing_mask'])) {
            throw new \Exception(self::CANT_FIND_CONFIG);
        }

        $listingUrl = preg_replace('/(\%\d)/', '%$1', $config['realtby']['listing_mask']);

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
        foreach ($links as $link) {
            $link = new Link($link, Link::SOURCE_REALT);
            $this->queue->enqueue($link);
        }

        return count($links);
    }
}
