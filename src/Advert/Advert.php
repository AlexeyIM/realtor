<?php

namespace Realtor\Advert;

/**
 * Class Advert
 * @package Realtor\Advert
 */
class Advert
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $price;

    /**
     * Title getter
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Title setter
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}
