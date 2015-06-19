<?php

namespace Realtor\Module\Onliner\Parser;

use Realtor\Parser\ParserInsterface;
use GuzzleHttp\Client;

/**
 * Class Listing
 * @package Realtor\Module\Onliner\Parser
 */
class Listing implements ParserInsterface
{
    /**
     * @param string $url
     * @return array
     */
    public function parsePage($url)
    {
        $client = new Client();
        $parameters = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ];
        $urlList = [];

        for ($i = 0; $i < 5; $i++) {
            $pageUrl = sprintf($url, $i + 1);
            $body = $client->get($pageUrl, $parameters)->getBody();
            $list = json_decode($body, true);
            foreach ($list['apartments'] as $item) {
                $urlList[] = $item['url'];
            }
        }

        return $urlList;
    }
}
