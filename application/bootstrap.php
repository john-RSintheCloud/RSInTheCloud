<?php
/**
 * Bootstrap
 *
 * PHP Version  PHP 5.3.10
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

//  Make sure app path is defined.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) ) . '/');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(
    //  put application path into include
    realpath(APPLICATION_PATH ),
    //  Put new library into include path
    realpath(APPLICATION_PATH . '../library/'),
    //  Put old include files in last
    realpath(APPLICATION_PATH . '../include/'),
    get_include_path()
)));

//  start timer
require_once 'timer.php';
$pageTimer = new timer();

//  Autoloader

spl_autoload_register(
  function ($pClassName) {
    require_once (APPLICATION_PATH . 'modules/' . str_replace("_", "/", $pClassName) . '.php');
  }
);

//  Dependency Injection
$container = new containers_Dic();
$container->init();


//  Load all the wrappers.
//  These contain legacy functions and DIC components.

require_once 'modules/config/wrapper.php';
require_once 'modules/error/wrapper.php';
require_once 'modules/database/wrapper.php';
require_once 'modules/language/wrapper.php';
require_once 'modules/plugins/wrapper.php';

require_once 'modules/server/wrapper.php';
require_once 'modules/files/wrapper.php';
require_once 'modules/session/wrapper.php';
require_once 'modules/string/wrapper.php';
require_once 'modules/url/wrapper.php';
require_once 'modules/resource/wrapper.php';
require_once 'modules/email/wrapper.php';
require_once 'modules/user/wrapper.php';
require_once 'modules/processLock/wrapper.php';

require_once 'views//helpers/wrapper.php';

//  DIC Plugins
// require_once 'modules/organisation/wrapper.php';

# Initialise hook for plugins
hook("initialise");

//  pull in the old includes
//  DEAD!  require_once "db.php";
//  DEAD!  require_once "general2.php";
require_once "collections_functions2.php";

