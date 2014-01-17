<?php

/**
 * @see Zim_Image_Driver_Exception
 */
# require_once 'Zend/Image/Driver/Exception.php';


/**
 * @see Zim_Image_Driver_Abstract
 */
# require_once 'Zend/Image/Driver/Abstract.php';

/**
 * GdImage driver
 *
 * @category    Zend
 * @package     Zim_Image
 * @subpackage  Zim_Image_Driver
 * @author      Leonid A Shagabutdinov <leonid@shagabutdinov.com>
 * @author      Stanislav Seletskiy <s.seletskiy@office.ngs.ru>
 */
class Zim_Image_Driver_Gd extends Zim_Image_Driver_Abstract
{
    /**
     * Load image from $fileName
     *
     * @throws Zim_Image_Driver_Exception
     * @param string $fileName Path to image
     */
    public function load( $fileName )
    {
        parent::load( $fileName );
 
        $this->_imageLoaded = false;
        
        $info = getimagesize( $fileName );
        switch( $this->_type ) {
            case 'jpg':
                $this->_image = imagecreatefromjpeg( $fileName );
                if ( $this->_image !== false ) {
                    $this->_imageLoaded = true;
                }
                break;
            case 'png':
                $this->_image = imagecreatefrompng( $fileName );
                if ( $this->_image !== false ) {
                    $this->_imageLoaded = true;
                }
                break;
            case 'gif':
                $this->_image = imagecreatefromgif( $fileName );
                if ( $this->_image !== false ) {
                    $this->_imageLoaded = true;
                }
                break;
        }
    }


    /**
     * Get image size
     *
     * @throws Zim_Image_Driver_Exception
     * @return array Format: array( width, height )
     */
    public function getSize()
    {
        if( $this->_image === null ) {
            throw new Zim_Image_Driver_Exception(
                'Trying to get size of non-loaded image'
            );
        }

        return array(
            imagesx( $this->_image ),
            imagesy( $this->_image )
        );
    }


    /**
     * Get image contents
     *
     * @return string
     */
    public function getBinary()
    {
        if( $this->_image === null ) {
            throw new Zim_Image_Driver_Exception(
                'Trying to get size of non-loaded image'
            );
        }
        
        ob_start();
        imagejpeg( $this->_image );
        return ob_get_clean();
    }


    /**
     *
     * @throws Zim_Image_Driver_Exception
     * @param string $file File name to save image
     * @param string $type File type to save
     */
    public function save( $file, $type = 'auto' )
    {
         if ( !$this->_image ) {
             throw new Zim_Image_Driver_Exception(
                 'Trying to save non-loaded image'
             );
         }

         if ( $type == 'auto' ) {
             $type = $this->getType();
         }

         switch ( $type ) {
             case 'jpg': case 'jpeg':
                return imagejpeg( $this->_image, $file );
                break;
            case 'png':
								imagesavealpha($this->_image,true);
               return imagepng( $this->_image, $file );
                break;
            case 'gif':
               return imagegif( $this->_image, $file );
                break;
             default:
                 throw new Zim_Image_Driver_Exception(
                    'Undefined image type: "' . $type . '"'
                 );
                 break;
         }
        
    }


    /**
     * Resize image into specified width and height
     *
     * @throws Zim_Image_Driver_Exception
     * @param int $width Width to resize
     * @param int $height Height to resize
     * @return bool
     */
    public function resize( $width, $height )
    {
        parent::resize( $width, $height );

        $imageSize = $this->getSize();
        $resizedImage = imagecreatetruecolor( $width, $height );
				if($this->_type == "png")
				{
					imagealphablending($resizedImage,true);
				}
        $successfull = imagecopyresized(
            $resizedImage, $this->_image,
            0, 0,
            0, 0,
            $width, $height,
            $imageSize[ 0 ], $imageSize[ 1 ]
        );

        unset( $this->_image );
        $this->_image = $resizedImage;

        return $successfull;
    }

    
    /**
     * Crop image into specified frame
     *
     * @throws Zim_Image_Driver_Exception
     * @param int $left Left point to crop from
     * @param int $top Top point to crop from
     * @param int $width Width to crop
     * @param int $height Height to crop
     * @return bool
     */
    public function crop( $left, $top, $width, $height )
    {
        parent::crop( $left, $top, $width, $height );

        $imageSize = $this->getSize();
        $croppedImage = imagecreatetruecolor( $width, $height );
				if($this->_type == "png")
				{
					imagealphablending($croppedImage,true);
				}
        $successfull = imagecopyresized(
            $croppedImage, $this->_image,
            0, 0,
            $left, $top,
            $width, $height,
            $left + $width, $top + $height
        );

        unset( $this->_image );
        $this->_image = $croppedImage;

        return $successfull;

    }

    /**
     * Link to gd-image resource
     *
     * @type resource
     */
    private $_image = null;
}
