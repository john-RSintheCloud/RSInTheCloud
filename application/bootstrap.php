<?php

/**
 * bootstrap
 * Common first chunk of code called;
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

// Ensure library/ is on include_path
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


require_once 'modules/database/wrapper.php';
require_once 'modules/error/wrapper.php';

require_once "db.php";
require_once "general2.php";
require_once "collections_functions2.php";

