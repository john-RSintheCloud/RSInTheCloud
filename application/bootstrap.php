<?php

/**
 * bootstrap
 * Common first chunk of code called;
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

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



require_once 'modules/database/wrapper.php';
require_once 'modules/error/wrapper.php';
include "db.php";
include "general.php";
include "collections_functions.php";
