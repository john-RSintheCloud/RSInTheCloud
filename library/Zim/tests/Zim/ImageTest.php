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
 * @version   $Id: ImageTest.php 52 2010-04-06 15:32:31Z leonid@shagabutdinov.com $
 */

/**
 * For single test run.
 */
if( ! defined( 'PHPUnit_MAIN_METHOD' ) ) {
    define( 'PHPUnit_MAIN_METHOD', 'Zend_ImageTest::main' );
}

/**
 * Test helper.
 */
require_once dirname( __FILE__ ) . '/../TestHelper.php';

/**
 * @see Zend_Image
 */
require_once 'Zend/Image.php';

/**
 * @category   Zend
 * @package    Zend_Image
 * @subpackage UnitTests
 * @author     Seletskiy Stanislav <s.seletskiy@office.ngs.ru>
 * @author     Leonid Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright  2010 NGS
 */
class Zend_ImageTest extends PHPUnit_Framework_Testcase
{
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite( __CLASS__ );
        $result = PHPUnit_TextUI_TestRunner::run( $suite );
    }


    public function setUp() {
        $this->_driverMock = $this->getMock( 'Zend_Image_Driver_Abstract' );
    }


    public function tearDown() {
        unset( $this->_driverMock );
    }


    public function testCanLoadByFileName()
    {
        $this->_driverMock->expects( $this->once() )->method( 'load' )
            ->with( 'doesnt-matter.jpg' );

        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
    }


    public function testCanLoadByZendImage()
    {
        $this->_driverMock->expects( $this->once() )->method( 'load' )
             ->will( $this->returnValue( 'ImageContents' ) );

        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
        $i2 = new Zend_Image( $i );

        $this->assertEquals( 
            $i->getDriver()->getBinary(),
            $i2->getDriver()->getBinary()
        );
    }


    public function testCanGetDriver()
    {
        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertEquals( $i->getDriver(), $this->_driverMock );
    }


    public function testCanSaveIntoFile()
    {
        $this->_driverMock->expects( $this->once() )->method( 'save' )
            ->with( 'doesnt-matter-2.jpg' )->will( $this->returnValue( true ) );

        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertTrue( $i->save( 'doesnt-matter-2.jpg' ) ); 
    }


    public function testCanGetImageContents()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getBinary' )
            ->will( $this->returnValue( 'ImageContents' ) );

        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertEquals( 'ImageContents', $i->getBinary());
    }


    public function testCanLoadImageFromBinary()
    {
        $this->markTestIncomplete();
    }


    public function testCanGetWidth()
    {
        $this->_driverMock->expects( $this->exactly( 2 ) )->method( 'getSize' )
            ->will( $this->returnValue( array( 50, 100 ) ) );

        $i = new Zend_Image( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertEquals( 50, $i->getWidth() );
        $this->assertEquals( 100, $i->getHeight() );
    }
}

if( PHPUnit_MAIN_METHOD == 'Zend_ImageTest::main' ) {
    Zend_ImageTest::main();
}
