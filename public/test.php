<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

require '../vendor/autoload.php';

use PHPHtmlParser\Dom;
use Realtor\Module\RealtBy\Parser\Advert;
use Noodlehaus\Config;

$config = new Config(__DIR__ . '/../config/main.json');

$page = 'http://realt.by/sale/flats/object/733021/';
$parser = new Advert();
$parser->setRules($config->get('rules'));
var_dump($parser->parsePage($page));