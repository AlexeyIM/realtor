<?php

namespace Realtor\Module\RealtBy\Parser;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use Realtor\Parser\AdvertParserInterface;
use Realtor\Advert\Advert as AdvertObject;
use Realtor\Utils\String;

/**
 * Class Advert
 * @package Realtor\Module\RealtBy\Parser
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

        $description = $advertDom->find('#content .description');
        if ($description->count()) {
            $this->checkDescription($description[0]);
        }

        $tables = $advertDom->find('table.object-view');

        foreach ($tables as $table) {
            $this->checkParameters($table);
        }

        $thumbs = $advertDom->find('table.object-view #thumbs');
        if ($thumbs->count()) {
            $this->checkImageCount($thumbs[0]);
        }

        $titleOblect = $advertDom->find('title', 0);
        $title = $titleOblect ? $titleOblect->text : 'no title';

        $advert = new AdvertObject();
        $advert->setTitle($title);
        return $advert;
    }

    /**
     * Description checker
     *
     * @param HtmlNode $node
     * @throws \Exception
     */
    protected function checkDescription(HtmlNode $node)
    {
        $stopWords = $this->rules['stop_words']['description'];
        $description = String::strToLower(strip_tags($node->innerhtml));

        if ($word = String::findWordsInString($description, $stopWords)) {
            $message = sprintf(self::ERROR_STOP_WORD_FOUND, $word);
            throw new \Exception($message);
        }
    }

    /**
     * Returns true if check was passed
     *
     * @param HtmlNode $node
     * @throws \Exception
     */
    protected function checkImageCount(HtmlNode $node)
    {
        $count = $node->find('img')->count();
        if ($count < $this->rules['realtby']['min_photo_count']) {
            $message = sprintf(self::ERROR_NOT_ENOUGH_PHOTOS, $count);
            throw new \Exception($message);
        }
    }

    /**
     * Returns true if check was passed
     *
     * @param HtmlNode $node
     * @throws \Exception
     */
    protected function checkParameters(HtmlNode $node)
    {
        $stopWords = $this->rules['stop_words']['parameters'];

        /** @var \PHPHtmlParser\Dom\HtmlNode $option */
        foreach ($node->find('tr') as $option) {
            /** @var \PHPHtmlParser\Dom\Collection $suboptions */
            $suboptions = $option->find('td');
            if ($suboptions->count() < 2) {
                continue;
            }

            list($titleNode, $valueNode) = $suboptions->toArray();
            $title = trim(String::strToLower($titleNode->text));
            $value = trim(String::strToLower(strip_tags($valueNode->innerHtml)));
            if (!empty($stopWords[$title])) {
                if ($word = String::findWordsInString($value, $stopWords[$title])) {
                    $message = sprintf(self::ERROR_STOP_WORD_IN_FIELD_FOUND, $word, $title);
                    throw new \Exception($message);
                }
            }
        }
    }
}