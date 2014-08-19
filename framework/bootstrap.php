<?php
todo
/**
 * Bootstrap for WW Framework
 *
 * PHP Version  PHP 5.5
 *
 * @author John Brookes 
*/

date_default_timezone_set('Europe/London');
	

// Define application environment - should be set in vhost
//  not currently used
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//  Make sure app path is defined.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)  . '/..') . '/');

//  echo APPLICATION_PATH; //  /var/www/core/

set_include_path(implode(PATH_SEPARATOR, array(
    //  put application path into include
    realpath(APPLICATION_PATH ),
    //  Put application code into include path
    realpath(APPLICATION_PATH . 'merlin/'),
    //  put shared library in include path
    realpath(APPLICATION_PATH . 'shared/'),
    //  Put framework into include path
    realpath(APPLICATION_PATH . 'framework/'),
    //  put vendor in include path - for composer
    realpath(APPLICATION_PATH . 'vendor/'),
    '.'
)));

//  Autoloader

spl_autoload_register(
  function ($pClassName) {
    $path = str_replace("_", "/", $pClassName) . '.php';
    if (file_exists(APPLICATION_PATH . $path)){
        require_once ($path);
    } else {
        $pathBits = explode( '\\' , $pClassName);
        //  Namespaces and Classes are UpperCamelCase
        //  Diirectories and files are camelCase
        //  so use lcFirst to convert
        $path = implode("/", array_map('lcfirst', $pathBits)) . '.php';
        if (file_exists(APPLICATION_PATH . $path)){
            require_once ($path);
        }
    }
  }
);


//  composer autoload (vendor/autoload.php)
require 'autoload.php';

//  and rub the app class

$app = new Framework\Application();

//  hopefully the app will exit / die, so we should never get here
throw new RuntimeException ('The app has finished unexpectedly');


function server(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . '/merlin/';
}
define ('SERVER_NAME', server());

