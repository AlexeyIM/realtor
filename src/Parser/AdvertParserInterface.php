<?php

namespace Realtor\Parser;

use \Realtor\Advert\Advert;

/**
 * Interface ParserInsterface
 * @package Realtor\Parser
 */
interface AdvertParserInterface extends ParserInsterface
{
    /**
     * @param array $rules
     * @return null
     */
    public function setRules(array $rules);

    /**
     * Returns advert object or null
     *
     * @param string $url
     * @return Advert|null
     */
    public function parsePage($url);
}
