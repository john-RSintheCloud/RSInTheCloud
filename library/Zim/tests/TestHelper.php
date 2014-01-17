<?php

/**
 * Zend Image
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category  Zend
 * @package   Zend_Image
 * @author    Stanislav Seletskiy <s.seletskiy@gmail.com>
 * @author    Leonid Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: TestHelper.php 45 2010-02-24 16:51:47Z s.seletskiy $
 */

date_default_timezone_set( 'Asia/Novosibirsk' );
error_reporting( E_ALL | E_STRICT );

define( 'ROOT_PATH', dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR );
define( 'LIBRARY_PATH', ROOT_PATH . 'library' . DIRECTORY_SEPARATOR );
define( 'TESTS_PATH', ROOT_PATH . 'tests' . DIRECTORY_SEPARATOR );

set_include_path(
	implode(
		PATH_SEPARATOR, array(
			LIBRARY_PATH, TESTS_PATH, get_include_path()
		)
	)
);

/*
 * PHPUnit
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addDirectoryToWhitelist( LIBRARY_PATH );
