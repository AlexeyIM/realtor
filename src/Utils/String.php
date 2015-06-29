<?php

namespace Realtor\Utils;

/**
 * Class String
 * @package Realtor\Utils
 */
class String
{
    /**
     * Converts case for non-latin symbols
     *
     * @param string $input
     * @return string
     */
    public static function strToLower($input)
    {
        $outputString = mb_convert_case($input, MB_CASE_LOWER, 'UTF-8') . '';

        return $outputString;
    }

    /**
     * Returns founded word, otherwise false
     *
     * @param string $haystack
     * @param string|array $needle
     * @return string|false
     * @throws \Exception
     */
    public static function findWordsInString($haystack, $needle)
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
}
