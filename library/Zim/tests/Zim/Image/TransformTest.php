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
 * @version   $Id: TransformTest.php 52 2010-04-06 15:32:31Z leonid@shagabutdinov.com $
 */


/**
 * For single test run.
 */
if ( ! defined( 'PHPUnit_MAIN_METHOD' ) ) {
    define( 'PHPUnit_MAIN_METHOD', 'Zend_Image_TransformTest::main' );
}

/**
 * Test helper.
 */
require_once dirname( __FILE__ ) . '/../../TestHelper.php';

/**
 * @see Zend_Image
 */
require_once 'Zend/Image.php';

/**
 * @see Zend_Image_Transform
 */
require_once 'Zend/Image/Transform.php';


/**
 * @category   Zend
 * @package    Zend_Image
 * @subpackage UnitTests
 * @author     Seletskiy Stanislav <s.seletskiy@office.ngs.ru>
 * @author     Leonid Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright  2010 NGS
 */
class Zend_Image_TransformTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite( __CLASS__ );
        $result = PHPUnit_TextUI_TestRunner::run( $suite );
    }


    public function setUp() 
    {
        $this->_driverMock = $this->getMock( 'Zend_Image_Driver_Abstract' );
    }


    public function tearDown() 
    {
        unset( $this->_driverMock );
    }



    public function testRaiseExceptionOnFitToWidthImageWithIncorrectWidth()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 0, 10 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->fitToWidth( 100 );
        } catch ( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnFitToWidthImageWithIncorrectHeight()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 10, 0 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->fitToWidth( 100 );
        } catch ( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnFitToHeightImageWithIncorrectWidth()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 0, 10 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->fitToHeight( 100 );
        } catch ( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnFitToHeightImageWithIncorrectHeight()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 10, 0 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->fitToHeight( 100 );
        } catch ( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testCanReturnZendImageOnFitToWidth()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->fitToWidth( 100 ) );
    }


    public function testCanFitToWidth()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' ) 
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 100, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitToWidth( 100 );
    }


    public function testCanReturnZendImageOnFitToHeight()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->fitToHeight( 50 ) );
    }


    public function testCanFitToHeight()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 100, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitToHeight( 50 );
    }


    public function testCanReturnZendImageOnFitIntoFrame()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->fitIn( 50, 50 ) );
    }


    public function testCanFitIntoFrameByWidth()
    {
        $this->_driverMock->expects( $this->any( 2 ) )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 50, 25 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitIn( 50, 50 );
    }


    public function testCanFitIntoFrameByHeight()
    {
        $this->_driverMock->expects( $this->any( 2 ) )->method( 'getSize' )
            ->will( $this->returnValue( array( 500, 1000 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 25, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitIn( 50, 50 );
    }


    public function testCanReturnZendImageOnFitOutFrame()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->fitOut( 50, 50 ) );
    }


    public function testCanFitOutOfFrameByHeight()
    {
        $this->_driverMock->expects( $this->any( 2 ) )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 100, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitOut( 50, 50 );
    }


    public function testCanFitOutOfFrameByWidth()
    {
        $this->_driverMock->expects( $this->any( 2 ) )->method( 'getSize' )
            ->will( $this->returnValue( array( 500, 1000 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 50, 100 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->fitOut( 50, 50 );
    }


    public function testCanReturnZendImageOnDiretions()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );

        $this->assertType( 'Zend_Image', $transform->left( 50 ) );
        $this->assertType( 'Zend_Image', $transform->right( 50 ) );
        $this->assertType( 'Zend_Image', $transform->center() );

        $this->assertType( 'Zend_Image', $transform->top( 50 ) );
        $this->assertType( 'Zend_Image', $transform->bottom( 50 ) );
        $this->assertType( 'Zend_Image', $transform->middle() );
    }


    public function testCanReturnZendImageOnCrop()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->crop( 50, 50 ) );
    }


    public function testCanCropLeftTop()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'crop' )
            ->with( 50, 10, 100, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->left( 50 )->top( 10 )->crop( 100, 50 );
    }


    public function testCanCropInNegativeDirectionTop()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 200, 300 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'crop' )
            ->with( $this->equalTo( 20 ), $this->equalTo( 30 ), 50, 60 );

        $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $image->left( 20 )->top( 30 + 60 )->crop( 50, -60 );
    }


    public function testCanCropInNegativeDirectionLeft()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 200, 300 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'crop' )
            ->with( $this->equalTo( 20 ), $this->equalTo( 30 ), 50, 60 );

        $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $image->left( 20 + 50 )->top( 30 )->crop( -50, 60 );
    }
/*

    public function testRaiseExceptionOnOutOfLeftBound()
    {
        $this->_driverMock->expects( $this->never() )->method( 'crop' );
        try {
            $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $image->left( 10 )->top( 10 )->crop( -20, 10 );
        } catch( Zend_Image_Transform_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnOutOfTopBounds()
    {
        $this->_driverMock->expects( $this->never() )->method( 'crop' );
        try {
            $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $image->left( 10 )->top( 10 )->crop( 10, -20 );
        } catch( Zend_Image_Transform_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnOutOfRightBound()
    {
        $this->_driverMock->expects( $this->never() )->method( 'crop' );
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 100, 200 ) ) );

        try {
            $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $image->left( 80 )->top( 10 )->crop( 40, 20 );
        } catch( Zend_Image_Transform_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnOutOfBottomBound()
    {
        $this->_driverMock->expects( $this->never() )->method( 'crop' );
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 100, 200 ) ) );

        try {
            $image = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $image->left( 80 )->top( 150 )->crop( 10, 60 );
        } catch( Zend_Image_Transform_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }

*/
    public function testCanCropRightBottom()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'crop' )
            ->with( 1000 - 50, 500 - 30, 30, 20 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->right( 50 )->bottom( 30 )->crop( 30, 20 );
    }


    public function testCanCropCenterMiddle()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );
        $this->_driverMock->expects( $this->once() )->method( 'crop' )
            ->with(
                round( ( 1000 - 100 ) / 2 ),
                round( ( 500 - 50 ) / 2 ),
                100, 50
            );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->center()->middle()->crop( 100, 50 );
    }


    public function testRaiseExceptionOnLeftRight()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->left(10)->right(10)->crop( 100, 50 );
        }
        catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnLeftCenter()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->center()->left(10)->crop( 100, 50 );
        }
        catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnRightCenter()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->center()->right(10)->crop( 100, 50 );
        }
        catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnTopBottom()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->top(10)->bottom(10)->crop( 100, 50 );
        }
        catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnTopMiddle()
    {
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->middle()->top(10)->crop( 100, 50 );
        }
        catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnBottomMiddle()
    {  
        $this->_driverMock->expects( $this->any() )->method( 'getSize' )
            ->will( $this->returnValue( array( 1000, 500 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->middle()->bottom(10)->crop( 100, 50 );
        } catch( Zend_Image_Transform_Exception $e ) {
            return ;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testCanReturnZendImageOnResize()
    {
        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $this->assertType( 'Zend_Image', $transform->resize( 100, 100 ) );
    }

    public function testCanResize()
    {
        $this->_driverMock->expects( $this->once() )->method( 'resize' )
            ->with( 100, 50 );

        $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
        $transform->resize( 100, 50 );
    }

/*
    public function testRaiseExceptionOnZeroWidth()
    {
        $this->_driverMock->expects( $this->once() )->method( 'getSize' )
            ->will( $this->returnValue( array( 10, 20 ) ) );

        try {
            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->fitToWidth( 0 );
        }  catch( Zend_Image_Transform_Exception $e ) {
            return;
        }

        $this->fail( 'Expected exception wasn\'n raised' );
    }
*/
/*
    public function testRaiseExceptionOnNegativeWidth()
    {
        try {
            $this->_driverMock->expects( $this->never() )->method( 'resize' );

            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->resize( -1, 50 );

        } catch ( Zend_Image_Transform_Exception $e) {
            return ;
        }
        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnNullHeight()
    {
        try {
            $this->_driverMock->expects( $this->never() )->method( 'resize' );

            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->resize( 1, 0 );

        } catch ( Zend_Image_Transform_Exception $e) {
            return ;
        }
        $this->fail( 'Expected exception wasn\'n raised' );
    }


    public function testRaiseExceptionOnNegativeHeight()
    {
        try {
            $this->_driverMock->expects( $this->never() )->method( 'resize' );

            $transform = new Zend_Image_Transform( 'doesnt-matter.jpg', $this->_driverMock );
            $transform->resize( 1, -1 );

        } catch ( Zend_Image_Transform_Exception $e) {
            return ;
        }
        $this->fail( 'Expected exception wasn\'n raised' );
    }*/
}


if ( PHPUnit_MAIN_METHOD == 'Zend_Image_TransformTest::main' ) {
    Zend_Image_TransformTest::main();
}
