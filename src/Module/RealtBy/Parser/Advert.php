<?php

namespace Realtor\Module\RealtBy\Parser;

use Realtor\Parser\IParser;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;

/**
 * Class Advert
 * @package Realtor\Module\RealtBy\Parser
 */
class Advert implements IParser
{
    /**
     * @var array
     */
    private $_rules = array();

    /**
     * Rules setter
     *
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;
    }
    /**
     * Returns true when all check were passed
     *
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function parsePage($url)
    {
        $advertDom = new Dom;
        $advertDom->loadFromUrl($url);

        $description = $advertDom->find('#content .description');
        if ($description->count()) {
            $this->_checkDescription($description[0]);
        }

        $tables = $advertDom->find('table.object-view');

        if ($tables->count()) {
            $this->_checkImageCount($tables[0]);
            $this->_checkParameters($tables[1]);
        }

        $title = $advertDom->find('title', 0);
        return $title ? $title->text : 'no title';
    }

    protected function _checkDescription(HtmlNode $node)
    {
        $stopWords = $this->_rules['stop_words']['description'];
        $description = $this->_strToLower(strip_tags($node->innerhtml));

        if ($word = $this->_findWordsInString($description, $stopWords)) {
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
    protected function _checkImageCount(HtmlNode $node)
    {
        if ($node->find('#thumbs img')->count() < $this->_rules['min_photo_count']) {
            throw new \Exception('Not enough photos');
        }
    }

    /**
     * Returns true if check was passed
     *
     * @param HtmlNode $node
     * @throws \Exception
     */
    protected function _checkParameters(HtmlNode $node)
    {
        $stopWords = $this->_rules['stop_words']['parameters'];

        /** @var \PHPHtmlParser\Dom\HtmlNode $option */
        foreach ($node->find('tr') as $option) {

            /** @var \PHPHtmlParser\Dom\Collection $suboptions */
            $suboptions = $option->find('td');
            if (!$suboptions->count()) {
                continue;
            }

            list($titleNode, $valueNode) = $suboptions->toArray();
            $title = trim($this->_strToLower($titleNode->text));
            $value = trim($this->_strToLower(strip_tags($valueNode->innerHtml)));
            if (!empty($stopWords[$title])) {
                if ($word = $this->_findWordsInString($value, $stopWords[$title])) {
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
    private function _findWordsInString($haystack, $needle)
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
    private function _strToLower($input)
    {
        $outputString = mb_convert_case($input, MB_CASE_LOWER, 'UTF-8') . '';

        return $outputString;
    }
}