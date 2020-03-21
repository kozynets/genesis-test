<?php declare(strict_types=1);

$root = __DIR__;
require_once 'vendor/autoload.php';

$cache = new \genesis\cache\Cache(new \genesis\cache\variants\StaticVariantCache(),
    new \genesis\cache\variants\FileVariantCache($root . '/file-cache.txt'));
$cache->set('key1', 'val1');
$cache->set('key2', 'val12323');
echo $cache->get('key1');
echo $cache->get('key2');

