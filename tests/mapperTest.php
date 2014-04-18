<?php

//echo 'hello world';

require_once "../application/bootstrap.php";

/**
 * @var database_mapper_user user mapper from DIC
 */
$source = $container['userMapper'];

echo 'fetch all users via mapper<br>';

$testSql = $source->fetch();
var_dump($testSql);

echo '<br><br>show the first record for bob<br>';
$test2 = $source->fetchOne(array('username' => 'bob'));
var_dump($test2);


echo '<br><br>copy and save as user john via mapper<br>';
$test2['ref'] = 0;
$test2['username'] = 'john';

var_dump ($source->saveArray($test2));

echo '<br><br>fetch all records for user john';
var_dump( $source->fetch(array('username' => 'john')));

echo 'Delete all records for user john';
var_dump( $source->delete(array('username' => 'john')));

echo 'update ref 1 to user admin';

var_dump ($source->saveArray( array(
    'ref' => '1',
    'username' => 'admin'
)));

echo 'show all';
var_dump($source->fetch());

//var_dump($container['db']->sqlQuery('show tables'));
//
//var_dump ($container['db']->fetchAll());
//
//var_dump($container['db']->sqlQuery('delete from user where email is null;'));
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
 * @var resource_model_assetAbstract
 */
//$asset = $container['model_asset'];
//
//echo " <br><br>deleting asset 'test' <br>";
//
//var_dump($asset->deleteAssetBySlug('test'));
//
//echo "asset->save(array(
//    'slug' => 'testa-11',
//    'date_modified' => 'now'
//)))";
//var_dump($asset->setOptions(array(
//    'slug' => 'testa-11',
//    'date_modified' => 'now'
//))->save());
//
//
//echo "asset 1 ref = $asset->ref<br>";
//echo "asset 1 slug = $asset->slug<br>";
//
//
//
///**
// * @var resource_model_assetAbstract
// */
//$ass2 = $container['model_asset'];
//
//
//echo " <br><br>asset 2 ref = $ass2->ref<br>";
//
//var_dump($ass2);
//
//$ass2->setOptions(array(
//    'slug' => 'testa-11',));
//
//var_dump ($ass2->hydrate());
//
//echo 'done';
//
