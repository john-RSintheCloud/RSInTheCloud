<?php

/**
 * Zend Image
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @author     Stanislav Seletskiy <s.seletskiy@gmail.com>
 * @author     Leonid Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright  Copyright (c) 2010
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: AllTests.php 45 2010-02-24 16:51:47Z s.seletskiy $
 */

if ( !defined( 'PHPUnit_MAIN_METHOD' )) {
    define( 'PHPUnit_MAIN_METHOD', 'Zend_AllTests::main' );
}

require_once dirname( __FILE__ ) . '/../TestHelper.php';

/**
 * @see Zend_AllTests
 */
require_once 'Zend/Image/AllTests.php';


/**
 * @see ZendTest
 */
require_once 'Zend/ImageTest.php';

/**
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Zend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run( self::suite() );
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();

        $suite->addTestSuite( 'Zend_Image_AllTests' );
        $suite->addTestSuite( 'Zend_ImageTest' );

        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'Zend_AllTests::main' ) {
    Zend_AllTests::main();
}
