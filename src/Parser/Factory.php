<?php

namespace Realtor\Parser;

use Realtor\Advert\Link;
use Realtor\Module\RealtBy\Parser\Advert as RealtParser;
use Realtor\Module\Onliner\Parser\Advert as OnlinerParser;

/**
 * Class Factory
 * @package Realtor\Parser
 */
class Factory
{
    /**
     * Returns advert parser
     *
     * @param string $source
     * @return AdvertParserInsterface
     */
    public static function createAdvertParser($source)
    {
        switch ($source) {
            case Link::SOURCE_REALT:
                $result = new RealtParser();
                break;
            case Link::SOURCE_ONLINER:
            default:
                $result = new OnlinerParser();
                break;
        }

        return $result;
    }
}
