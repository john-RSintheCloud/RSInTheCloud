<?php

/**
 * resources Wrapper
 * A wrapper round the resource handling functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */



//  Dependency Injection
//  Our DIC needs to know about all our classes,
//  but this is an attempt to add them in an orderly manner.
//  Once the system is all OO, this can be moved to the main container->init method.

//  Asset model requires table injection
$container['model_asset'] = function ($c) {
    $model = new asset_model_assetAbstract();
    $model->setDbClass($c['table_asset']);
    return $model;
        };

//  Basket model requires table injection
$container['model_basket'] = function ($c) {
    $model = new asset_model_basket();
    $model->setDbClass($c['table_basket']);
    return $model;
        };

