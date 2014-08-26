<?php
/**
 * Bootstrap
 *
 * PHP Version  PHP 5.3.10
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RS - generic
 * @subpackage Refactor
*/

error_reporting(E_ALL);


//  Make sure app path is defined.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) ) . '/');

// Define application environment
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(implode(PATH_SEPARATOR, array(
    //  put application path into include
    realpath(APPLICATION_PATH),
    //  Put modules into include path
    APPLICATION_PATH . 'modules/',
    //  Put new library into include path
    realpath(APPLICATION_PATH . '../library/'),
    //  Put old include files in last
    realpath(APPLICATION_PATH . '../include/'),
    //  and current folder
    '.'
)));

//  Autoloader
spl_autoload_register(
  function ($pClassName) {
    require_once ( str_replace("_", "/", $pClassName) . '.php');
  }
);
//  Dependency Injection
$container = new containers_Dic();
$container['ApplicationPath'] = APPLICATION_PATH;
$container->init();

//  start timer
$pageTimer = $container['timer']->start();

//  Set baseURL  - the host name.
//  If the application is running in a folder, set this in the config file as $baseFolder
if (!defined('BASE_URL')) {
    $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://")
        . $_SERVER['HTTP_HOST'];
//        . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $root);
}



//  Load all the modules/*/wrappers.
//  These contain legacy functions and DIC components.

require_once 'config/wrapper.php';
require_once 'error/wrapper.php';
require_once 'database/wrapper.php';
require_once 'language/wrapper.php';
//  not using legacy plugins, but this provides stubs for called functions
 require_once 'plugins/wrapper.php';


require_once 'server/wrapper.php';
require_once 'files/wrapper.php';
require_once 'session/wrapper.php';
require_once 'string/wrapper.php';
require_once 'url/wrapper.php';
require_once 'resource/wrapper.php';
require_once 'email/wrapper.php';
require_once 'user/wrapper.php';
require_once 'processLock/wrapper.php';

require_once 'views/helpers/wrapper.php';

//  optional DI Plugins defined in secure.config.php

// var_dump($container['config']); die;
$diPlugins = explode(',', $container['config']->diPlugins);
foreach ($diPlugins as $plug) {
    if (! empty($plug)){
        require_once  $plug . '/wrapper.php';
    }
}
# Initialise hook for plugins
//  hook("initialise");
//  pull in the old includes
require_once "collections_functions2.php";

