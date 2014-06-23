<?php

//echo 'hello world';

require_once "../application/bootstrap.php";

/**
 * @var user_model_user user model from DIC
 */
$user1 = $container['user'];

echo 'dump empty user 1<br>';

var_dump($user1->toArray());

/**
 * @var user_model_user user model from DIC
 */
$user2 = $container['user'];

echo '<br><br>show the first record for bob<br>';
$test1 = $user1->fetchOne(array('username' => 'bob'))
        ->toArray();
//var_dump($test1);
var_dump($user1->isLoggedIn());

echo 'dump empty user 2<br>';

var_dump($user2->toArray());

echo '<br><br>show the ref and username for the first record <br>';
$test2 = $user2->fetchOne(array())
        ->toArray();
//var_dump($test2);
var_dump($user2->ref);
var_dump($user2->userName);

echo 'create and dump user 3<br>';
$user3 = $container['user'];
var_dump($user3->toArray());

echo '<br><br>copy and save as user john via mapper<br>';
$user3->ref = 0;
$user3->username = 'john';
var_dump ($user3->save()->toArray());

echo 'update  to user bob';

$user3->username = 'bob';
var_dump ($user3->save()->toArray());


echo 'Delete this record ';
var_dump( $user3->delete());

die;

echo 'Delete all records for user john';
var_dump( $user1->delete(array('username' => 'john')));


echo 'fetch all users via mapper<br>';

$testSql = $user1->fetch();
var_dump($testSql);


echo '<br><br>fetch all records for user john';
var_dump( $user1->fetch(array('username' => 'john')));


echo 'show all';
var_dump($user1->fetch());

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
