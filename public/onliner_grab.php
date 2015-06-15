<?php

$url = 'https://pk.api.onliner.by/search/apartments?number_of_rooms[]=1&number_of_rooms[]=2&price[min]=10000&price[max]=90000&currency=usd&bounds[lb][lat]=53.77103665374234&bounds[lb][long]=27.321624755859375&bounds[rt][lat]=54.02471335178857&bounds[rt][long]=27.802276611328125&page=%d';

$links = [];

function getContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$url");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// 5 times will be enough
for ($i = 0; $i < 5; $i++) {
    $pageUrl = sprintf($url, $i + 1);
    $list = json_decode(getContent($pageUrl), true);
    foreach ($list['apartments'] as $item) {
        // add $item['url'] to the queue
        $links[] = $item['url'];
    }
}

foreach ($links as $key => $link) {
    $advert = getContent($link);
    preg_match('/(\d{4}) года/u', $advert, $matches);
    if (isset($matches[1]) && $matches[1] <= 2000) {
        unset($links[$key]);
        continue;
    }

    echo sprintf('<a href="%s">%s</a><br>', $links[$key], $links[$key]);
}

