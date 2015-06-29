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
     * @var int
     */
    private $pricePerMeter;

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

    /**
     * Price per meter setter
     *
     * @param int $price
     * @return $this
     */
    public function setPricePerMeter($price)
    {
        $this->pricePerMeter = $price;
        return $this;
    }

    /**
     * Price per meter getter
     *
     * @return int
     */
    public function getPricePerMeter()
    {
        return $this->pricePerMeter;
    }
}
