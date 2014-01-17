<?php

echo 'hello world';

require_once "../application/bootstrap.php";

var_dump($container['db']->sqlQuery('display tables'));

var_dump ($container['db']->fetchAll());

var_dump($container['db']->sqlQuery('select * from random'));

var_dump ($container['db']->fetchAll());

var_dump ($container['db']->fetchSome());

var_dump ($container['db']->fetchSome(4,1));

var_dump ($container['db']->fetchSome(4,5));

var_dump ($container['db']->fetchAll('select * from random where ref = 2'));

var_dump ($container['db']->getStats());

