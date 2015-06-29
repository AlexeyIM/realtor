<?php

namespace Realtor\Parser;

use \Realtor\Advert\Advert;

/**
 * Interface ParserInsterface
 * @package Realtor\Parser
 */
interface AdvertParserInterface extends ParserInsterface
{
    const ERROR_NOT_ENOUGH_PHOTOS = 'Not enough photos (%d)';
    const ERROR_STOP_WORD_FOUND = '"%s" stop word has been found in the description';
    const ERROR_STOP_WORD_IN_FIELD_FOUND = '"%s" stop word has been found in "%s" field';
    const ERROR_WRONG_YEAR = '"%s" is a wrong year';

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
