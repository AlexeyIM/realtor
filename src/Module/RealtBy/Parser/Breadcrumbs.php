<?php

namespace Realtor\Module\RealtBy\Parser;

use Realtor\Parser\ParserInsterface;
use PHPHtmlParser\Dom;

/**
 * Class Breadcrumbs
 * @package Realtor\Module\RealtBy\Parser
 */
class Breadcrumbs implements ParserInsterface
{
    /**
     * Returns number of pages based on paginator page element
     *
     * @param string $url
     * @return int
     * @throws \Exception
     */
    public function parsePage($url)
    {
        $listingDom = new Dom;
        $listingDom->loadFromUrl($url);

        /** @var \PHPHtmlParser\Dom\Collection $pageLinks */
        $pageLinks = $listingDom->find('#uedb-page-browser ul li a');

        $numberOfPages = $pageLinks->offsetGet($pageLinks->count() - 1)->text;

        if ($numberOfPages <= 0) {
            throw new \Exception('Can\'t find breadcrumbs element on the page');
        }

        return $numberOfPages;
    }
}