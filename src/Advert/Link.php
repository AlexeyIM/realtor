<?php

namespace Realtor\Advert;

/**
 * Class Link
 * @package Realtor\Advert
 */
class Link
{
    const SOURCE_REALT = 'realt';
    const SOURCE_ONLINER = 'onliner';

    /**
     * Initials
     *
     * @param string $url
     * @param string $source
     */
    public function __construct($url, $source)
    {
        $this->url = $url;
        $this->source = $source;
    }

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $url;

    /**
     * @return string
     */
    public function __toString()
    {
        return serialize($this);
    }

    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Source getter
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
