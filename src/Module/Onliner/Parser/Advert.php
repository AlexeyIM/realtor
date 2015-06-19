<?php

namespace Realtor\Module\Onliner\Parser;

use PHPHtmlParser\Dom;
use Realtor\Parser\AdvertParserInterface;
use Realtor\Advert\Advert as AdvertObject;

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

        $stopWords = $this->rules['stop_words']['description'];
        $body = $advertDom->find('.arenda-apartment', 0);
        $description = $this->strToLower(strip_tags($body->innerhtml));

        if ($word = $this->findWordsInString($description, $stopWords)) {
            $message = sprintf('"%s" stop word has been found in the description', $word);
            throw new \Exception($message);
        }

        preg_match('/(\d{4}) года/u', $description, $matches);
        if (isset($matches[1]) && $matches[1] < $this->rules['min_year']) {
            $message = sprintf('"%s" is a wrong year', $matches[1]);
            throw new \Exception($message);
        }

        $titleOblect = $advertDom->find('title', 0);
        $title = $titleOblect ? $titleOblect->text : 'no title';

        $advert = new AdvertObject();
        $advert->setTitle($title);
        return $advert;
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
