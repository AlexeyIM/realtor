<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

require '../vendor/autoload.php';


/*
$queue = new Realtor\Queue\Storage(31337);
$queue->enqueue('aaaa');
var_dump($queue->dequeue());
die;
*/

use PHPHtmlParser\Dom;
use Realtor\Module\RealtBy\Parser\Advert;
use Noodlehaus\Config;

$config = new Config(__DIR__ . '/../config/main.json');

$page = 'http://realt.by/sale/flats/object/727469/';
$parser = new Advert();
$parser->setRules($config->get('rules'));
var_dump($parser->parsePage($page));

/*$advertDom = file_get_dom(reset($urlList));
$photos = $advertDom('#thumbs a');
if (count($photos) > 2) {

}*/