<?php

/**
 * For single test run.
 */
if ( ! defined( 'PHPUnit_MAIN_METHOD' ) ) {
    define( 'PHPUnit_MAIN_METHOD', 'Zend_Image_Driver_AbstractTest::main' );
}

/**
 * Test helper.
 */
require_once dirname( __FILE__ ) . '/../../../TestHelper.php';

/**
 * @see Zend_Image_Driver_AbstractStub
 */
require_once dirname( __FILE__ ) . '/AbstractStub.php';

/**
 * Testcase fir abstract class for drivers
 *
 * @category    Zend
 * @package     Zend_Image
 * @subpackage  Zend_Image_Driver
 * @author      Stanislav Seletskiy <s.seletskiy@office.ngs.ru>
 * @author      Leonid A Shagabutdinov <leonid@shagabutdinov.com>
 */
class Zend_Image_Driver_AbstractTest extends PHPUnit_Framework_Testcase
{
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite( __CLASS__ );
        $result = PHPUnit_TextUI_TestRunner::run( $suite );
    }

    public function setUp() {}
    public function tearDown() {}
//
//    public function testRaiseExceptionOnLoad()
//    {
//            $driver = new Zend_Image_Driver_AbstractStub();
//
//        try {
//            $driver->load( 'does not matter' );
//        } catch( Zend_Image_Driver_Exception $e ) {
//            return ;
//        }
//        $this->fail( 'Expected exception wasn\'n raised' );
//    }
    public function testRaiseExceptionOnResizeToNullWidth()
    {
        $driver = new Zend_Image_Driver_AbstractStub();

        try {
            $driver->resize( 0, 50 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnResizeToNegativeWidth()
    {
        $driver = new Zend_Image_Driver_AbstractStub();

        try {
            $driver->resize( -1, 50 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }
 
    public function testRaiseExceptionOnResizeToNullHeight()
    {
        $driver = new Zend_Image_Driver_AbstractStub();

        try {
            $driver->resize( 50, 0 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnResizeToNegativeHeight()
    {
        $driver = new Zend_Image_Driver_AbstractStub();

        try {
            $driver->resize( 50, -1 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnCropOutOfHeight()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 200, 30 );

        try {
            $driver->crop( 10, 20, 30, 40 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnCropOutOfWidth()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 20, 300 );

        try {
            $driver->crop( 10, 20, 30, 40 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnCropFromNegativeLeft()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 200, 300 );

        try {
            $driver->crop( -50, 20, 30, 40 );
        } catch( Zend_Image_Driver_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnCropFromNegativeTop()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 200, 300 );

        try {
            $driver->crop( 10, -50, 30, 40 );
        } catch( Zend_Image_Driver_Exception $e ) {
           return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }

    

    public function testRaiseExceptionOnCropToNegativeWidth()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 200, 300 );

        try {
            $driver->crop( 210, 50, -30, 40 );
        } catch( Zend_Image_Driver_Exception $e ) {
           return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    
    public function testRaiseExceptionOnCropToNegativeHeight()
    {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->setSize( 200, 300 );

        try {
            $driver->crop( 110, 50, 30, -40 );
        } catch( Zend_Image_Driver_Exception $e ) {
           return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testGetTypeJpeg(  ) {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->load( dirname( __FILE__ ) . '/_files/get-type-test.jpg' );

        $this->assertEquals(
            $driver->getType(),
            'jpg'
        );
    }

    public function testGetTypePng(  ) {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->load( dirname( __FILE__ ) . '/_files/get-type-test.png' );

        $this->assertEquals(
            $driver->getType(),
            'png'
        );
    }



    public function testGetTypeGif(  ) {
        $driver = new Zend_Image_Driver_AbstractStub();
        $driver->load( dirname( __FILE__ ) . '/_files/get-type-test.gif' );

        $this->assertEquals(
            $driver->getType(),
            'gif'
        );
    }
}



if ( PHPUnit_MAIN_METHOD == 'Zend_Image_Driver_AbstractTest::main' ) {
    Zend_Image_Driver_AbstractTest::main();
}
