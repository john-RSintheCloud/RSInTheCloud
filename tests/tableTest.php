<?php

//echo 'hello world';

require_once "../application/bootstrap.php";

echo 'fetch all users<br>';

$testSql = $container['userTable']->fetch();
var_dump($testSql);

echo '<br><br>show the first user<br>';
$test2 = reset($testSql);
var_dump($test2);


echo '<br><br>copy and save as user john<br>';
$test2['ref'] = 0;
$test2['username'] = 'john';

var_dump ($container['userTable']->save($test2)->ref);

echo '<br><br>fetch all records for user john';
$testSql = $container['userTable']->fetch(array('username' => 'john'));
var_dump($testSql);
$container['userTable']->delete(array('username' => 'john'));

var_dump ($container['userTable']->save( array(
    'ref' => '1',
    'username' => 'admin'
)));

echo 'delete john';
$container['userTable']->delete(array('username' => 'john'));

echo 'show all';
var_dump($container['userTable']->fetch());

echo 'done';

