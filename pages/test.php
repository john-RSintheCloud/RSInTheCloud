<?php

echo 'hello world';

require_once "../application/bootstrap.php";

var_dump($container['db']->sqlQuery('show tables'));
//
//var_dump ($container['db']->fetchAll());
//
//var_dump($container['db']->sqlQuery('select * from random'));
//
//var_dump ($container['db']->fetchAll());
//
//var_dump ($container['db']->fetchSome());
//
//var_dump ($container['db']->fetchSome(4,1));
//
//var_dump ($container['db']->fetchSome(4,5));
//
//var_dump ($container['db']->fetchAll('select * from random where ref = 2'));
//
//var_dump ($container['db']->getStats());
//


/**
 * @var database_table_asset
 */
$asset = $container['table_asset'];

var_dump($asset->deleteAssetBySlug('test'));

var_dump($asset->save(array(
    'slug' => 'testa-11',
    'date_modified' => 'now'
)));

var_dump($asset);
var_dump ($asset->fetchAssets());

echo 'done';

