<?php

/**
 * For single testcase run.
 */
if ( ! defined( 'PHPUnit_MAIN_METHOD' ) ) {
    define( 'PHPUnit_MAIN_METHOD', 'Zend_Image_Transform_IntegrationTest::main' );
}

/**
 * Test helper.
 */
require_once dirname( __FILE__ ) . '/../../../TestHelper.php';

/**
 * @see Zend_Image_Transform
 */
require_once 'Zend/Image/Transform.php';

/**
 * @see Zend_Image_Transform
 */
require_once 'Zend/Image/Driver/Gd.php';

/**
 * Integration test for Transform subsystem
 *
 * @package     Zend_Image
 * @subpackage  Zend_Image_UnitTests
 * @author      Stanislav Seletskiy <s.seletskiy@office.ngs.ru>
 * @author      Leonid A Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright   2010
 */
class Zend_Image_Transform_IntegrationTest extends PHPUnit_Framework_Testcase
{
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite( __CLASS__ );
        $result = PHPUnit_TextUI_TestRunner::run( $suite );
    }

    public function setUp() {}
    public function tearDown() {}

    public function testCanFitToWifth()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->fitToWidth( 100 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file100x144 )
        );
    }


    public function testCanFitToHeight()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->fitToHeight( 100 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file70x100 )
        );
    }


    public function testCanFitInto()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->fitIn( 100, 100 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file70x100 )
        );
    }

    public function testCanFitOut()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->fitOut( 100, 100 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file100x144 )
        );
    }

    public function testCanCrop()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->crop( 100, 100 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file100x100 )
        );
    }

    public function testCanResize()
    {
        $zendImage = new Zend_Image_Transform( 
            dirname( __FILE__ ) . '/' . $this->_file300x431, 
            new Zend_Image_Driver_Gd() 
        );

        $this->assertEquals(
            md5( $zendImage->resize( 150, 150 )->getBinary() ),
            md5_file( dirname( __FILE__ ) . '/' . $this->_file150x150 )
        );
    }

    
    /**
     * @type string
     */
    private $_file150x150 = '_files/alice-150x150.jpg';

    /**
     * @type string
     */
    private $_file100x100 = '_files/alice-100x100.jpg';

    /**
     * @type string
     */
    private $_file300x431 = '_files/alice.jpg';

    /**
     * @type string
     */
    private $_file100x144 = '_files/alice-100x144.jpg';

    /**
     * @type string
     */
    private $_file70x100 = '_files/alice-70x100.jpg';
}

if ( PHPUnit_MAIN_METHOD == 'Zend_Image_Transform_IntegrationTest::main' ) {
    Zend_Image_Transform_IntegrationTest::main();
}
