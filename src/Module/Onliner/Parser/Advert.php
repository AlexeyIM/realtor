<?php

namespace Realtor\Module\Onliner\Parser;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use Realtor\Parser\AdvertParserInterface;
use Realtor\Advert\Advert as AdvertObject;
use Realtor\Utils\String;

/**
 * Class Advert
 * @package Realtor\Module\Onliner\Parser
 */
class Advert implements AdvertParserInterface
{
    /**
     * @var array
     */
    private $rules = array();

    /**
     * Rules setter
     *
     * @param array $rules
     * @return null
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Returns Advert object when all check were passed
     *
     * @param string $url
     * @return Advert
     * @throws \Exception
     */
    public function parsePage($url)
    {
        $advertDom = new Dom;
        $advertDom->loadFromUrl($url);

        $thumbsWrapper = $advertDom->find('.apartment-cover__thumbnails-inner');
        if ($thumbsWrapper->count() > 0) {
            $this->checkImageCount($thumbsWrapper[0]);
        }

        $stopWords = $this->rules['stop_words']['description'];
        $body = $advertDom->find('.arenda-apartment', 0);
        $description = String::strToLower(strip_tags($body->innerhtml));

        if ($word = String::findWordsInString($description, $stopWords)) {
            $message = sprintf(self::ERROR_STOP_WORD_FOUND, $word);
            throw new \Exception($message);
        }

        preg_match('/(\d{4}) года/u', $description, $matches);
        if (isset($matches[1]) && $matches[1] < $this->rules['min_year']) {
            $message = sprintf(self::ERROR_WRONG_YEAR, $matches[1]);
            throw new \Exception($message);
        }

        $titleOblect = $advertDom->find('title', 0);
        $title = $titleOblect ? $titleOblect->text : 'no title';

        $advert = new AdvertObject();
        $advert->setTitle($title);
        return $advert;
    }

    /**
     * Returns true if check was passed
     *
     * @param HtmlNode $node
     * @throws \Exception
     */
    protected function checkImageCount(HtmlNode $node)
    {
        $count = $node->find('.apartment-cover__thumbnail')->count();
        if ($count < $this->rules['onliner']['min_photo_count']) {
            $message = sprintf(self::ERROR_NOT_ENOUGH_PHOTOS, $count);
            throw new \Exception($message);
        }
    }
}
