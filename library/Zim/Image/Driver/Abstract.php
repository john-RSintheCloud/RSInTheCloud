<?php

/**
 * @see Zim_Image_Driver_Exception
 */
# require_once 'Zend/Image/Driver/Exception.php';

/**
 * Abstract class for drivers
 *
 * @category    Zend
 * @package     Zim_Image
 * @subpackage  Zim_Image_Driver
 * @author      Stanislav Seletskiy <s.seletskiy@office.ngs.ru>
 * @author      Leonid A Shagabutdinov <leonid@shagabutdinov.com>
 */
abstract class Zim_Image_Driver_Abstract
{
    /**
     *
     * @var boolean is image loaded?
     */
    protected $_imageLoaded = false;

    /**
     * Type of current image (jpeg, png or etc.)
     *
     * @var string
     */
    protected $_type = '';



    /**
     * Save image to filename
     *
     * @throws Zim_Image_Driver_Exception
     * @param string $filename
		 * @param string type
     * @return bool
     */
    public abstract function save( $filename ,$type='auto');


    /**
     * Get image contents
     *
     * @throws Zim_Image_Driver_Exception
     * @return string
     */
    public abstract function getBinary();


    /**
     * Get image size as array(width, height)
     *
     * @throws Zim_Image_Driver_Exception
     * @return array Format: array(width, height)
     */
    public abstract function getSize();


    /**
     * File to load image from
     *
     * @throws Zim_Image_Driver_Exception
     * @param string $fileName
     */
    public function load( $fileName )
    {
        $this->_type = $this->_getFileType( $fileName );
    }


    /**
     *
     * @throws Zim_Image_Driver_Exception
     * @param string $fileName
     * @return string jpg | png | gif
     */
    protected function _getFileType( $fileName )
    {
        if ( !is_readable( $fileName ) ) {
            throw new Zim_Image_Driver_Exception(
                'Cannot read file "' . $fileName . '"'
            );
        }

        $fileHandle = fopen( $fileName, 'r' );
        $bytes = fread( $fileHandle, 20 );
        fclose( $fileHandle );

        $jpegMatch = "\xff\xd8\xff\xe0";

        if ( mb_strstr( $bytes, $jpegMatch ) ) {
            return 'jpg';
        }

        $jpegMatch = "\xff\xd8\xff\xe1";

        if ( mb_strstr( $bytes, $jpegMatch ) ) {
            return 'jpg';
        }

        $pngMatch = "\x89PNG\x0d\x0a\x1a\x0a";

        if ( mb_strstr( $bytes, $pngMatch ) ) {
            return 'png';
        }

         $gifMatch = "GIF8";

         if ( mb_strstr( $bytes, $gifMatch ) ) {
             return 'gif';
         }

         return false;
    }

    /**
     * Resize image to specified coordinats
     *
     * @throws Zim_Image_Driver_Exception
     * @param int $width
     * @param int $height
     */
    public function resize( $width, $height )
    {
        if ( $width <= 0 ) {
            throw new Zim_Image_Driver_Exception(
                'Width can not be zero or negative'
            );
        }

        if ( $height <= 0 ) {
            throw new Zim_Image_Driver_Exception(
                'Height can not be zero or negative'
            );
        }
    }


    /**
     * Crop image to specified coordinates
     *
     * Test crop area - limit to image area
     * If width and / or height are negative,
     * adjust top and left and invert width and height
     *
     * If top or left are negative, adjust width and height
     * and set top / left to zero
     *
     * @throws Zim_Image_Driver_Exception
     * @param int $width
     * @param int $height
     * @param int $targetWidth
     * @param int $targetHeight
     */
    public function crop( $left, $top, $targetWidth, $targetHeight )
    {
        //  allow negative height and width
        if ( $targetHeight <= 0 ) {
            $top += $targetHeight;
            $targetHeight = - $targetHeight;

//            throw new Zim_Image_Driver_Exception(
//                'Target height can not be 0 or negative'
//            );
        }
        if ( $targetWidth <= 0 ) {
            $left += $targetWidth;
            $targetWidth = - $targetWidth;

//            throw new Zim_Image_Driver_Exception(
//                'Target width can not be 0 or negative'
//            );
        }

        //  If out of bounds, crop to edge
        if ( $left < 0 ) { 
            $targetWidth += $left;
            $left = 0;
        }
//            throw new Zim_Image_Driver_Exception(
//                "Trying to crop from ($left, $top). Offset can't " .
//                    "be negative."
//            );
//        }

        if ( $top < 0 ) { 
            $targetHeight += $top;
            $top = 0;

        }
//            throw new Zim_Image_Driver_Exception(
//                "Trying to crop from ($left, $top). Offset can't " .
//                    "be negative."
//            );
//        }

        //  Get bottom and right bounds and limit to them
        list( $sourceWidth, $sourceHeight ) = $this->getSize();

        if ( $top + $targetHeight > $sourceHeight ) {
            $targetHeight = $sourceHeight - $top ;
//            throw new Zim_Image_Driver_Exception(
//                'Trying to crop to (' . ( $left + $targetWidth ) . ', ' .
//                    ( $top + $targetHeight ) . '). Out of bottom bound.'
//            );
        }

        if ( $left + $targetWidth > $sourceWidth ) {
            $targetWidth = $sourceWidth - $left;
//            throw new Zim_Image_Driver_Exception(
//                'Trying to crop to (' . ( $left + $targetWidth ) . ', ' .
//                    ( $top + $targetHeight ) . '). Out of right bound.'
//            );
        }

        //  the instantiated class must contain a crop command
        //  appropriate to the driver
        $this->conCrop((int) $left, (int) $top, (int) $targetWidth, (int) $targetHeight);


    }


    /**
     * Crop image to specified coordinates using specific driver.
     *
     * Assumes all parameters are valid
     *
     * @throws Zim_Image_Driver_Exception
     * @param int $width
     * @param int $height
     * @param int $targetWidth
     * @param int $targetHeight
     */
    public abstract function conCrop($left, $top, $targetWidth, $targetHeight );



    protected function getTypeFileName( $fileName )
    {
        $info = pathinfo( $fileName );

        return $info['extension'];
    }


    /**
     * Check if image was loaded
     *
     * @return bool
     */
    public function isImageLoaded()
    {
        return $this->_imageLoaded;
    }


    /**
     * Get type of current image
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }


}
