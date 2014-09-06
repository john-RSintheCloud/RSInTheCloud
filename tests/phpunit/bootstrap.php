<?php
// turn on all errors
error_reporting(E_ALL);

$basePath = realpath(dirname(__FILE__)  . '/../..') . '/';

//  echo $basePath; //  /var/www/RS/OORS

set_include_path(implode(PATH_SEPARATOR, array(
    //  put application path into include
    $basePath ,
    //  Put application code into include path
    $basePath . 'application/',
    //  put shared library in include path
    $basePath . 'application/modules/',
    //  Put framework into include path
    $basePath . 'library/',
    //  put include in include path - for legacy
    $basePath . 'include/',
    //vendor
    $basePath . 'vendor/',
    //phpunit path
    $basePath . 'vendor/phpunit/phpunit/src/',
    //  current folder
    '.'

)));

date_default_timezone_set('Europe/London');
	
//  Autoloader

spl_autoload_register( function ($pClassName) {
    //  for framework:
    $class = $pClassName;
    $path = str_replace("_", "/", $class) . '.php';
    @include_once ($path);
    // Check to see whether the include found the class
    if (!class_exists($pClassName, false)) {
        //  try again
        $pathBits = explode('\\', $pClassName);
        //  Namespaces and Classes are UpperCamelCase
        //  Diirectories and files are camelCase
        //  so use lcFirst to convert
        $path = implode("/", array_map('lcfirst', $pathBits)) . '.php';
        include_once ($path);
        //  drop through to composer autoloader
//        if (!class_exists($pClassName, false)) {
//            throw new RuntimeException("Unable to load class: $pClassName");
//        }
}}
);
//  composer autoload (vendor)
require 'autoload.php';


