<?php

namespace Realtor\Module\RealtBy\Parser;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use Realtor\Parser\AdvertParserInterface;
use Realtor\Advert\Advert as AdvertObject;

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

        if ($tables->count()) {
            $this->checkImageCount($tables[0]);
            $this->checkParameters($tables[1]);
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
        $description = $this->strToLower(strip_tags($node->innerhtml));

        if ($word = $this->findWordsInString($description, $stopWords)) {
            $message = sprintf('"%s" stop word has been found in the description', $word);
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
        if ($node->find('#thumbs img')->count() < $this->rules['min_photo_count']) {
            throw new \Exception('Not enough photos');
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
            if (!$suboptions->count()) {
                continue;
            }

            list($titleNode, $valueNode) = $suboptions->toArray();
            $title = trim($this->strToLower($titleNode->text));
            $value = trim($this->strToLower(strip_tags($valueNode->innerHtml)));
            if (!empty($stopWords[$title])) {
                if ($word = $this->findWordsInString($value, $stopWords[$title])) {
                    $message = sprintf('"%s" stop word has been found in "%s" field', $word, $title);
                    throw new \Exception($message);
                }
            }
        }
    }

    /**
     * Returns founded word, otherwise false
     *
     * @param string $haystack
     * @param string|array $needle
     * @return string|false
     * @throws \Exception
     */
    private function findWordsInString($haystack, $needle)
    {
        $result = false;

        if (is_string($needle)) {
            if (strpos($haystack, $needle) !== false) {
                $result = $needle;
            }
        } elseif (is_array($needle)) {
            foreach ($needle as $word) {
                if (strpos($haystack, $word) !== false) {
                    $result = $word;
                    break;
                }
            }
        } else {
            throw new \Exception('Wrong <neelde> type for word search');
        }

        return $result;
    }

    /**
     * Converts case for non-latin symbols
     *
     * @param string $input
     * @return string
     */
    private function strToLower($input)
    {
        $outputString = mb_convert_case($input, MB_CASE_LOWER, 'UTF-8') . '';

        return $outputString;
    }
}