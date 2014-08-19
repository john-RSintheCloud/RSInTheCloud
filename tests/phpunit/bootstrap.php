<?php
// turn on all errors
error_reporting(E_ALL);

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)  . '/../..') . '/');

//  echo APPLICATION_PATH; //  /var/www/RS/OORS

set_include_path(implode(PATH_SEPARATOR, array(
    //  put application path into include
    realpath(APPLICATION_PATH ),
    //  Put application code into include path
    realpath(APPLICATION_PATH . 'application/'),
    //  put shared library in include path
    realpath(APPLICATION_PATH . 'application/modules/'),
    //  Put framework into include path
    realpath(APPLICATION_PATH . 'library/'),
    //  put include in include path - for legacy
    realpath(APPLICATION_PATH . 'include/'),
    //phpunit path
//    '/usr/share/php/',
    '.'

)));

date_default_timezone_set('Europe/London');
	
//  Autoloader

spl_autoload_register(
  function ($pClassName) {
    require_once ( str_replace("_", "/", $pClassName) . '.php');
  }
);
//  composer autoload (vendor)
// require 'autoload.php';


