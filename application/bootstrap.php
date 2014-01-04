<?php

/**
 * Bootstrap
 *
 * PHP Version  PHP 5.3.10
 *
 * @author John Brookes <john@RSintheClouds.com>
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

//  Headers should be set in the layout script, not here.
//  Suppress Headers is set in access.php and file.php before calling bootstrap.
if (!isset($suppress_headers) || !$suppress_headers)
	{
	header("Expires: Mon, 26 Jul 2012 05:00:00 GMT");    // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	}

# *** LOAD CONFIG ***
# Load the default config first, if it exists,
# so any new settings are present even if missing from config.php.
# Throw an exception if not found
require_once APPLICATION_PATH . "../_config/config.default.php";
# Load the real config
if (!file_exists(APPLICATION_PATH . "../_config/config.php")) {
    header ("Location: pages/setup.php" );
    die;
}
require_once APPLICATION_PATH . "../_config/config.php";

# Set the storage directory and URL if not already set.
if (!isset($storagedir)) {die('Storage dir not set in config');}


# Set time limit from config value
set_time_limit($php_time_limit);

# Set a base URL part consisting of the part after the server name, i.e. for absolute URLs and cookie paths.
$baseurl=str_replace(" ","%20",$baseurl);
$bs=explode("/",$baseurl);
$bs=array_slice($bs,3);
$baseurl_short="/" . join("/",$bs) . (count($bs)>0?"/":"");


$pagename=str_replace(".php","",  basename($_SERVER["PHP_SELF"]));

# Set character set.
//  this should be in layout
if (($pagename!="download") && ($pagename!="graph")) {header("Content-Type: text/html; charset=UTF-8");} // Make sure we're using UTF-8.
#------------------------------------------------------

require_once 'modules/error/wrapper.php';
require_once 'modules/files/wrapper.php';
require_once 'modules/database/wrapper.php';
require_once 'modules/session/wrapper.php';
require_once 'modules/language/wrapper.php';
require_once 'modules/plugins/wrapper.php';
require_once 'modules/view/wrapper.php';
require_once 'modules/url/wrapper.php';



# Initialise hook for plugins
hook("initialise");

//  pull in the old includes
//  DEAD!  require_once "db.php";
require_once "general2.php";
require_once "collections_functions2.php";


