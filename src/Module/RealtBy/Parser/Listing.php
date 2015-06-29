<?php

namespace Realtor\Module\RealtBy\Parser;

use Realtor\Parser\ParserInsterface;
use PHPHtmlParser\Dom;

/**
 * Class Listing
 * @package Realtor\Module\RealtBy\Parser
 */
class Listing implements ParserInsterface
{
    const CANT_FIND_ADVERTS = 'Can\'t find any adverts on the page';

    /**
     * Returns number of pages based on paginator page element
     *
     * @param string $url
     * @return array
     * @throws \Exception
     */
    public function parsePage($url)
    {
        $listingDom = new Dom;
        $listingDom->loadFromUrl($url);
        $linkList = $listingDom->find('#photolist .row .thumb-frame a');

        /** @var \PHPHtmlParser\Dom\HtmlNode $link */
        foreach ($linkList as $link) {
            $urlList[] = $link->getAttribute('href');
        }

        if (!isset($urlList)) {
            throw new \Exception(self::CANT_FIND_ADVERTS);
        }

        return $urlList;
    }
}